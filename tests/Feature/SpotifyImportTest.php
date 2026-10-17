<?php

namespace Tests\Feature;

use App\Console\Commands\SpotifySyncClean;
use App\Models\Media;
use App\Models\User;
use App\Models\UserMedia;
use App\Models\UserSpotifyToken;
use App\Repos\SpotifyRepo;
use App\Services\Spotify\SpotifySyncService;
use App\Services\SpotifyAPI;
use Cumulati\Monolog\LogContext;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Madcoda\Youtube\Facades\Youtube;
use Mockery;
use ReflectionProperty;
use SpotifyWebAPI\Request;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIException;
use Tests\CreatesApplication;

class SpotifyImportTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'logging.default' => 'null']);
        DB::purge('sqlite');
        LogContext::setDefaultLogger(Log::channel('null')->getLogger());

        Schema::create('user_spotify_tokens', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('access_token');
            $table->string('refresh_token');
            $table->timestamps();
        });
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('origin');
            $table->string('type');
            $table->string('subtype');
            $table->string('index');
            $table->integer('user_id');
            $table->string('title');
            $table->string('thumbnail');
            $table->string('spotify_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('media_meta', function (Blueprint $table) {
            $table->unsignedBigInteger('media_id')->primary();
            $table->string('title')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('artist_id')->nullable();
            $table->string('spotify_id')->nullable();
            $table->timestamps();
        });
        Schema::create('user_media', function (Blueprint $table) {
            $table->id();
            $table->integer('media_id');
            $table->integer('user_id');
            $table->timestamp('pushed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('user_spotify_imports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('media_id')->nullable();
            $table->string('track_name');
            $table->string('track_artist');
            $table->string('search_query');
            $table->boolean('imported')->default(false);
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });
        Schema::create('title_filters', function (Blueprint $table) {
            $table->string('value');
        });
    }

    public function test_spotify_sync_is_scheduled_hourly_without_overlapping(): void
    {
        $events = collect($this->app->make(Schedule::class)->events())
            ->filter(fn ($event) => str_contains($event->command ?? '', 'spotify:sync'));

        $this->assertCount(1, $events);
        $this->assertSame('0 * * * *', $events->first()->expression);
        $this->assertTrue($events->first()->withoutOverlapping);
    }

    public function test_a_track_with_no_youtube_results_is_left_unimported(): void
    {
        Youtube::shouldReceive('searchAdvanced')->once()->andReturn([]);

        $this->assertFalse($this->service()->syncTrack(1, $this->track('missing')));
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseHas('user_spotify_imports', ['imported' => false]);
    }

    public function test_spotify_import_cleans_promotional_title_annotations(): void
    {
        $video = $this->video();
        $video->snippet->title = 'Artist song [Official Full Screen]';
        Youtube::shouldReceive('searchAdvanced')->once()->andReturn([$video]);

        $this->assertTrue($this->service()->syncTrack(1, $this->track('song')));
        $this->assertDatabaseHas('media', ['title' => 'Artist song']);
        $this->assertDatabaseHas('media_meta', ['title' => 'Artist song']);
    }

    public function test_youtube_collection_cleans_promotional_title_annotations(): void
    {
        $video = $this->video();
        $video->snippet->title = 'Artist song [Official Full Screen]';
        Youtube::shouldReceive('getVideoInfo')->once()->with('video')->andReturn($video);
        $user = new User;
        $user->id = 1;

        $this->actingAs($user)->postJson('/api/media/collect', ['videoId' => 'video'])->assertOk();

        $this->assertDatabaseHas('media', ['title' => 'Artist song']);
        $this->assertDatabaseHas('media_meta', ['title' => 'Artist song']);
    }

    public function test_hourly_filter_cleans_existing_titles_and_metadata_without_custom_filters(): void
    {
        Schema::create('media_temp_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
        $media = $this->media('song');
        $media->title = 'Artist song [Official Full Screen]';
        $media->save();
        DB::table('media_meta')->where('media_id', $media->id)->update(['title' => $media->title]);
        DB::table('media_temp_items')->insert(['title' => 'Other song (Official Music Video)']);

        $this->assertSame(0, Artisan::call('filter:run'));

        $this->assertDatabaseHas('media', ['id' => $media->id, 'title' => 'Artist song']);
        $this->assertDatabaseHas('media_meta', ['media_id' => $media->id, 'title' => 'Artist song']);
        $this->assertDatabaseHas('media_temp_items', ['title' => 'Other song']);
    }

    public function test_new_import_saves_spotify_metadata_and_can_be_reused_without_searching(): void
    {
        Youtube::shouldReceive('searchAdvanced')->once()->andReturn([$this->video()]);
        $service = $this->service();

        $this->assertTrue($service->syncTrack(1, $this->track('song')));
        $media = Media::firstOrFail();
        $this->assertSame('song', $media->spotify_id);
        $this->assertDatabaseHas('media_meta', ['media_id' => $media->id, 'spotify_id' => 'song']);
        $this->assertDatabaseHas('artists', ['name' => 'Artist']);

        $this->assertTrue($service->syncTrack(2, $this->track('song')));
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('user_media', ['user_id' => 2, 'media_id' => $media->id]);
    }

    public function test_previous_imports_with_missing_metadata_are_reused_and_repaired(): void
    {
        $media = $this->media('song');
        Youtube::shouldReceive('searchAdvanced')->never();

        $this->assertTrue($this->service()->syncTrack(1, $this->track('song')));
        $this->assertDatabaseHas('media_meta', ['media_id' => $media->id, 'spotify_id' => 'song']);
        $this->assertDatabaseHas('user_media', ['user_id' => 1, 'media_id' => $media->id]);
    }

    public function test_tracks_without_spotify_ids_are_skipped_without_collecting_unrelated_media(): void
    {
        $this->media(null);
        Youtube::shouldReceive('searchAdvanced')->never();

        $this->assertFalse($this->service()->syncTrack(1, $this->track(null)));
        $this->assertDatabaseCount('user_media', 0);
    }

    public function test_legacy_metadata_only_spotify_ids_are_reused_and_copied_to_media(): void
    {
        $media = $this->media(null);
        DB::table('media_meta')->where('media_id', $media->id)->update(['spotify_id' => 'song']);
        Youtube::shouldReceive('searchAdvanced')->never();

        $this->assertTrue($this->service()->syncTrack(1, $this->track('song')));
        $this->assertSame('song', $media->fresh()->spotify_id);
        $this->assertDatabaseCount('media', 1);
    }

    public function test_import_playlist_is_found_beyond_the_first_fifty_playlists(): void
    {
        $api = Mockery::mock(SpotifyWebAPI::class);
        $firstPage = array_fill(0, 50, (object) ['id' => 'other', 'name' => 'Other']);
        $api->shouldReceive('getUserPlaylists')->once()->with('owner', ['limit' => 50, 'offset' => 0])
            ->andReturn((object) ['items' => $firstPage, 'next' => 'next-page']);
        $api->shouldReceive('getUserPlaylists')->once()->with('owner', ['limit' => 50, 'offset' => 50])
            ->andReturn((object) ['items' => [(object) ['id' => 'import', 'name' => 'DS Import']], 'next' => null]);
        $api->shouldReceive('createPlaylist')->never();

        $playlist = $this->service()->getOrCreateImportPlaylist($api, (object) ['id' => 'owner']);
        $this->assertSame('import', $playlist->id);
    }

    public function test_sync_imports_later_track_pages_and_continues_after_empty_search_results(): void
    {
        $api = $this->playlistApi();
        $api->shouldReceive('getPlaylistTracks')->once()->with('import', ['limit' => 50, 'offset' => 0])
            ->andReturn((object) ['items' => [$this->track('missing')], 'next' => 'next-page']);
        $api->shouldReceive('getPlaylistTracks')->once()->with('import', ['limit' => 50, 'offset' => 1])
            ->andReturn((object) ['items' => [$this->track('song')], 'next' => null]);
        Youtube::shouldReceive('searchAdvanced')->once()->with(Mockery::on(fn ($options) => $options['q'] === 'Artist missing'))->andReturn([]);
        Youtube::shouldReceive('searchAdvanced')->once()->with(Mockery::on(fn ($options) => $options['q'] === 'Artist song'))->andReturn([$this->video()]);
        $service = Mockery::mock(SpotifySyncService::class, [new SpotifyRepo])->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('apiForToken')->andReturn($api);

        $service->syncByToken($this->token(1));

        $this->assertDatabaseHas('media', ['spotify_id' => 'song']);
        $this->assertDatabaseHas('user_spotify_imports', ['track_name' => 'missing', 'imported' => false]);
    }

    public function test_cleanup_reads_every_page_before_deleting_tracks_in_batches(): void
    {
        $api = $this->playlistApi();
        $tracks = [];
        for ($index = 0; $index < 101; $index++) {
            $media = $this->media('song-'.$index);
            UserMedia::create(['user_id' => 1, 'media_id' => $media->id]);
            $tracks[] = $this->track('song-'.$index);
        }
        foreach (array_chunk($tracks, 50) as $page => $items) {
            $api->shouldReceive('getPlaylistTracks')->once()->ordered()
                ->with('import', ['limit' => 50, 'offset' => $page * 50])
                ->andReturn((object) ['items' => $items, 'next' => $page < 2 ? 'next-page' : null]);
        }
        $api->shouldReceive('deletePlaylistTracks')->once()->ordered()
            ->with('import', Mockery::on(fn ($data) => count($data['tracks']) === 100))->andReturn('snapshot');
        $api->shouldReceive('deletePlaylistTracks')->once()->ordered()
            ->with('import', ['tracks' => [['uri' => 'spotify:track:song-100']]])->andReturn('snapshot');
        $this->token(1);
        $this->registerCleanup($api);

        $this->assertSame(0, Artisan::call('spotify:sync-clean', ['--user-id' => 1]));
    }

    public function test_cleanup_failure_for_one_user_does_not_stop_other_users(): void
    {
        $this->token(1);
        $this->token(2);
        $invalidApi = Mockery::mock(SpotifyWebAPI::class);
        $invalidApi->shouldReceive('me')->once()->andThrow(new SpotifyWebAPIException('Invalid connection', 401));
        $api = $this->playlistApi();
        $api->shouldReceive('getPlaylistTracks')->once()->andReturn((object) ['items' => [], 'next' => null]);
        $api->shouldReceive('deletePlaylistTracks')->never();
        $this->registerCleanup($api, $invalidApi);

        $this->assertSame(1, Artisan::call('spotify:sync-clean'));
    }

    public function test_cleanup_only_removes_tracks_collected_by_the_current_user(): void
    {
        $owned = $this->media(null);
        DB::table('media_meta')->where('media_id', $owned->id)->update(['spotify_id' => 'owned']);
        UserMedia::create(['user_id' => 1, 'media_id' => $owned->id]);
        $other = $this->media('other');
        UserMedia::create(['user_id' => 2, 'media_id' => $other->id]);
        $this->token(1);
        $api = $this->playlistApi();
        $api->shouldReceive('getPlaylistTracks')->once()->andReturn((object) ['items' => [
            $this->track('owned'), $this->track('other'), $this->track('unknown'), $this->track(null), (object) ['track' => null],
        ], 'next' => null]);
        $api->shouldReceive('deletePlaylistTracks')->once()
            ->with('import', ['tracks' => [['uri' => 'spotify:track:owned']]])->andReturn('snapshot');
        $this->registerCleanup($api);

        $this->assertSame(0, Artisan::call('spotify:sync-clean', ['--user-id' => 1]));
    }

    public function test_bulk_sync_continues_after_a_user_failure_and_cleans_successful_users(): void
    {
        $this->token(1);
        $this->token(2);
        $sync = Mockery::mock(SpotifySyncService::class);
        $sync->shouldReceive('syncByToken')->once()->with(Mockery::on(fn ($token) => $token->user_id === 1))
            ->andThrow(new SpotifyWebAPIException('Invalid connection', 401));
        $sync->shouldReceive('syncByToken')->once()->with(Mockery::on(fn ($token) => $token->user_id === 2));
        $this->app->instance(SpotifySyncService::class, $sync);
        $api = $this->playlistApi();
        $api->shouldReceive('getPlaylistTracks')->once()->andReturn((object) ['items' => [], 'next' => null]);
        $api->shouldReceive('deletePlaylistTracks')->never();
        $this->registerCleanup($api);

        $this->assertSame(1, Artisan::call('spotify:sync'));
    }

    public function test_lazy_token_refresh_persists_rotated_tokens_before_retrying_the_api_call(): void
    {
        $this->assertRefreshPersists('rotated-refresh');
    }

    public function test_token_refresh_preserves_the_refresh_token_when_spotify_does_not_rotate_it(): void
    {
        $this->assertRefreshPersists(null);
    }

    private function assertRefreshPersists(?string $refreshToken): void
    {
        $token = $this->token(1);
        $api = SpotifyAPI::getInstanceWithToken($token);
        $session = (new ReflectionProperty(SpotifyWebAPI::class, 'session'))->getValue($api);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('setOptions');
        $request->shouldReceive('api')->once()->ordered()
            ->andThrow(new SpotifyWebAPIException(SpotifyWebAPIException::TOKEN_EXPIRED, 401));
        $response = (object) ['access_token' => 'new-access', 'expires_in' => 3600];
        if ($refreshToken !== null) {
            $response->refresh_token = $refreshToken;
        }
        $request->shouldReceive('account')->once()->ordered()->andReturn(['body' => $response]);
        $request->shouldReceive('api')->once()->ordered()->andReturnUsing(function () use ($token, $refreshToken) {
            $this->assertDatabaseHas('user_spotify_tokens', [
                'id' => $token->id, 'access_token' => 'new-access', 'refresh_token' => $refreshToken ?? 'old-refresh',
            ]);

            return ['body' => (object) ['id' => 'owner']];
        });
        (new ReflectionProperty(SpotifyWebAPI::class, 'request'))->setValue($api, $request);
        (new ReflectionProperty($session, 'request'))->setValue($session, $request);

        $this->assertSame('owner', $api->me()->id);
    }

    private function service(): SpotifySyncService
    {
        return new SpotifySyncService(new SpotifyRepo);
    }

    private function token(int $userId): UserSpotifyToken
    {
        $token = new UserSpotifyToken;
        $token->user_id = $userId;
        $token->access_token = 'old-access';
        $token->refresh_token = 'old-refresh';
        $token->save();

        return $token;
    }

    private function media(?string $spotifyId): Media
    {
        $media = Media::create([
            'origin' => 'spotify#import', 'type' => 'youtube', 'subtype' => 'video',
            'index' => 'video-'.Media::count(), 'user_id' => 1, 'title' => 'Artist song', 'thumbnail' => 'image',
        ]);
        $media->spotify_id = $spotifyId;
        $media->save();

        return $media;
    }

    private function track(?string $id): object
    {
        return (object) ['track' => (object) [
            'id' => $id, 'name' => $id ?? 'Local song', 'uri' => 'spotify:track:'.$id,
            'artists' => [(object) ['id' => 'artist', 'name' => 'Artist']],
        ]];
    }

    private function video(): object
    {
        return (object) ['id' => (object) ['videoId' => 'video'], 'snippet' => (object) [
            'title' => 'Artist song', 'thumbnails' => (object) ['default' => (object) ['url' => 'image']],
        ]];
    }

    private function playlistApi(): SpotifyWebAPI
    {
        $api = Mockery::mock(SpotifyWebAPI::class);
        $api->shouldReceive('me')->andReturn((object) ['id' => 'owner']);
        $api->shouldReceive('getUserPlaylists')->andReturn((object) [
            'items' => [(object) ['id' => 'import', 'name' => 'DS Import']], 'next' => null,
        ]);

        return $api;
    }

    private function registerCleanup(SpotifyWebAPI $api, ?SpotifyWebAPI $invalidApi = null): void
    {
        $command = Mockery::mock(SpotifySyncClean::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $command->__construct();
        $command->shouldReceive('apiForToken')->andReturnUsing(
            fn ($token) => $invalidApi !== null && $token->user_id === 1 ? $invalidApi : $api
        );
        Artisan::getFacadeRoot()->registerCommand($command);
    }
}
