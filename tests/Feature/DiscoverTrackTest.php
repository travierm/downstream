<?php

namespace Tests\Feature;

use App\MediaType\YoutubeVideo;
use App\Models\User;
use App\Services\DailyMixService;
use App\Services\Discovery\YoutubeTrackResolver;
use App\Services\SpotifyAPI;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use SpotifyWebAPI\SpotifyWebAPI;
use Tests\CreatesApplication;

class DiscoverTrackTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        // This endpoint contract test uses an isolated DB, never external APIs.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'database']);
        DB::purge('sqlite');
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('index');
            $table->string('title');
            $table->string('spotify_id')->nullable();
            $table->softDeletes();
        });
        Schema::create('user_media_plays', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('media_id');
            $table->timestamps();
        });
        Schema::create('user_media', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('media_id');
            $table->timestamps();
            $table->softDeletes();
        });
        require_once __DIR__.'/../../database/migrations/2018_02_18_174432_create_cache_table.php';
        (new \CreateCacheTable)->up();
        (require __DIR__.'/../../database/migrations/2026_10_17_000000_create_cache_locks_table.php')->up();
        Cache::flush();
        $user = new User;
        $user->id = 1;
        $this->actingAs($user);
    }

    protected function tearDown(): void
    {
        (new \ReflectionProperty(SpotifyAPI::class, 'api'))->setValue(null, null);
        parent::tearDown();
    }

    public function test_cold_request_uses_database_lock_and_repeat_request_uses_cache(): void
    {
        DB::table('media')->insert(['id' => 123, 'index' => 'seed-video', 'title' => 'Seed', 'spotify_id' => 'seed']);
        $api = Mockery::mock(SpotifyWebAPI::class);
        $api->shouldReceive('getRecommendations')->once()->andReturn((object) ['tracks' => [(object) ['id' => 'track']]]);
        (new \ReflectionProperty(SpotifyAPI::class, 'api'))->setValue(null, $api);
        $resolver = Mockery::mock(YoutubeTrackResolver::class);
        $resolver->shouldReceive('resolve')->once()->andReturn([]);
        $this->app->instance(YoutubeTrackResolver::class, $resolver);

        $this->getJson('/api/discover/track/seed-video')->assertOk()->assertJsonCount(1, 'items');
        $this->getJson('/api/discover/track/seed-video')->assertOk()->assertJsonCount(1, 'items');
        $this->assertSame(0, DB::table('cache_locks')->count(), 'Lock is released after generation');
    }

    private function seedMixTrack(int $id = 123): void
    {
        DB::table('media')->insert(['id' => $id, 'index' => 'seed-video-'.$id, 'title' => 'Seed']);
        $video = new YoutubeVideo;
        $video->videoId = $id === 123 ? 'recommended' : 'recommended-'.$id;
        $video->title = 'Recommended';
        $video->thumbnail = 'https://example.test/thumb.jpg';
        $video->guid = 'guid_recommended';
        Cache::put('similar_tracks:v2:'.$id.':6', [$video], now()->addDays(10));
    }

    public function test_daily_mix_generates_from_recent_collection_and_then_uses_cache(): void
    {
        $this->seedMixTrack();
        DB::table('user_media')->insert(['user_id' => 1, 'media_id' => 123, 'created_at' => now()]);
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.0.videoId', 'recommended');
        DB::table('user_media')->delete();
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.0.videoId', 'recommended');
        $this->assertSame(0, DB::table('cache_locks')->count());
    }

    public function test_daily_mix_can_reuse_seeds_when_all_eligible_collections_are_on_cooldown(): void
    {
        $this->seedMixTrack();
        DB::table('user_media')->insert(['user_id' => 1, 'media_id' => 123, 'created_at' => now()]);
        Cache::put('daily-mix-cooldowns:v2:1', [123 => now()->addDays(3)->timestamp], now()->addDays(3));
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.0.videoId', 'recommended');
    }

    public function test_empty_account_gets_a_useful_response_and_can_get_a_mix_after_collecting(): void
    {
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos', [])->assertJsonStructure(['message']);
        $this->seedMixTrack();
        DB::table('user_media')->insert(['user_id' => 1, 'media_id' => 123, 'created_at' => now()]);
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.0.videoId', 'recommended');
    }

    public function test_daily_mix_does_not_use_another_users_mix_or_seeds(): void
    {
        $this->seedMixTrack();
        DB::table('user_media')->insert(['user_id' => 2, 'media_id' => 123, 'created_at' => now()]);
        Cache::put('daily-mix:v2:2', ['videos' => [['videoId' => 'private-mix']]], 60);
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos', []);
    }

    public function test_daily_mix_failure_does_not_cache_a_mix_or_advance_ignored_seeds(): void
    {
        DB::table('media')->insert(['id' => 123, 'index' => 'seed-video', 'title' => 'Seed', 'spotify_id' => 'seed']);
        DB::table('user_media')->insert(['user_id' => 1, 'media_id' => 123, 'created_at' => now()]);
        $api = Mockery::mock(SpotifyWebAPI::class);
        $api->shouldReceive('getRecommendations')->once()->andReturn((object) ['tracks' => [(object) ['id' => 'track']]]);
        (new \ReflectionProperty(SpotifyAPI::class, 'api'))->setValue(null, $api);
        $resolver = Mockery::mock(YoutubeTrackResolver::class);
        $resolver->shouldReceive('resolve')->once()->andThrow(new \RuntimeException('provider unavailable'));
        $this->app->instance(YoutubeTrackResolver::class, $resolver);

        $this->getJson('/api/daily-mix')->assertStatus(500);
        $this->assertNull(Cache::get('daily-mix:v2:1'));
        $this->assertNull(Cache::get('daily-mix-cooldowns:v2:1'));
        $this->assertSame(0, DB::table('cache_locks')->count());
    }

    public function test_daily_mix_uses_only_distinct_recent_collections_newest_first(): void
    {
        $this->freezeTime();
        foreach (range(1, 12) as $id) {
            $this->seedMixTrack($id);
            DB::table('user_media')->insert([
                'user_id' => $id === 12 ? 2 : 1, 'media_id' => $id,
                'created_at' => now()->subDays(12 - $id),
                'deleted_at' => $id === 11 ? now() : null,
            ]);
        }
        DB::table('media')->where('id', 10)->update(['deleted_at' => now()]);
        DB::table('user_media')->insert(['user_id' => 1, 'media_id' => 9, 'created_at' => now()]);

        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.*.videoId', [
            'recommended-9', 'recommended-8', 'recommended-7',
            'recommended-6', 'recommended-5', 'recommended-4',
        ]);
        $this->assertCount(6, Cache::get('daily-mix-cooldowns:v2:1'));
    }

    public function test_daily_mix_ignores_listening_history_and_collections_older_than_thirty_days(): void
    {
        $this->freezeTime();
        $this->seedMixTrack();
        DB::table('user_media_plays')->insert(['user_id' => 1, 'media_id' => 123, 'created_at' => now()]);
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos', []);
        DB::table('user_media')->insert(['user_id' => 1, 'media_id' => 123, 'created_at' => now()->subDays(30)->subSecond()]);
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos', []);
        DB::table('user_media')->update(['created_at' => now()->subDays(30)]);
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.0.videoId', 'recommended');
    }

    public function test_seed_cooldowns_expire_independently_after_three_days(): void
    {
        $this->freezeTime();
        $this->seedMixTrack(1);
        $this->seedMixTrack(2);
        foreach ([1, 2] as $id) {
            DB::table('user_media')->insert(['user_id' => 1, 'media_id' => $id, 'created_at' => now()->subDays($id)]);
        }
        $expiresAt = now()->addDay()->timestamp;
        Cache::put('daily-mix-cooldowns:v2:1', [1 => $expiresAt], now()->addDay());
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.*.videoId', ['recommended-2']);
        $this->assertSame($expiresAt, Cache::get('daily-mix-cooldowns:v2:1')[1]);
        $this->assertSame(now()->addDays(3)->timestamp, Cache::get('daily-mix-cooldowns:v2:1')[2]);

        $this->travel(1)->days();
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.*.videoId', ['recommended-1']);
        $this->travel(2)->days();
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.*.videoId', ['recommended-2']);
    }

    public function test_daily_mix_ignores_legacy_cache_entries(): void
    {
        $this->seedMixTrack();
        DB::table('user_media')->insert(['user_id' => 1, 'media_id' => 123, 'created_at' => now()]);
        Cache::put('daily-mix-1', ['videos' => [['videoId' => 'legacy']]], 60);
        Cache::put('daily-mix-ignore-1', [123], 60);
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.0.videoId', 'recommended');
    }

    public function test_empty_recommendations_expire_after_five_minutes_without_advancing_cooldowns(): void
    {
        $this->freezeTime();
        $this->seedMixTrack();
        DB::table('user_media')->insert(['user_id' => 1, 'media_id' => 123, 'created_at' => now()]);
        Cache::put('similar_tracks:v2:123:6', [], now()->addMinutes(5));
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos', []);
        $this->assertNull(Cache::get('daily-mix-cooldowns:v2:1'));
        $this->assertSame(['videos' => []], Cache::get('daily-mix:v2:1'));
        $this->seedMixTrack(124);
        Cache::put('similar_tracks:v2:123:6', Cache::get('similar_tracks:v2:124:6'), now()->addDay());
        $this->travel(5)->minutes();
        $this->getJson('/api/daily-mix')->assertOk()->assertJsonPath('videos.0.videoId', 'recommended-124');
    }

    public function test_daily_mix_lock_timeout_returns_a_retryable_response(): void
    {
        $service = Mockery::mock(DailyMixService::class);
        $service->shouldReceive('getDailyMix')->once()->andThrow(new LockTimeoutException);
        $this->app->instance(DailyMixService::class, $service);
        $this->getJson('/api/daily-mix')->assertStatus(503)->assertHeader('Retry-After', '5')->assertJsonStructure(['message']);
    }

    public function test_daily_mix_requires_authentication(): void
    {
        auth()->forgetGuards();
        $this->getJson('/api/daily-mix')->assertUnauthorized();
    }

    public function test_can_get_bad_video_id_error(): void
    {
        $this->getJson('/api/discover/track/unknown')->assertStatus(500)->assertJson(['message' => 'Bad video id given']);
    }

    public function test_can_get_similar_track_results(): void
    {
        DB::table('media')->insert(['id' => 123, 'index' => 'seed-video', 'title' => 'Seed']);
        $video = new YoutubeVideo;
        $video->videoId = 'recommendation';
        $video->title = 'Recommended';
        $video->thumbnail = 'https://example.test/thumb.jpg';
        $video->guid = 'guid_recommendation';
        Cache::put('similar_tracks:v2:123:24', [$video], 60);

        $this->getJson('/api/discover/track/seed-video')->assertOk()
            ->assertJsonCount(2, 'items')
            ->assertJsonPath('items.0.index', 'seed-video')
            ->assertJsonPath('items.1.videoId', 'recommendation')
            ->assertJsonPath('items.1.guid', 'guid_recommendation');
    }
}
