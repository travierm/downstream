<?php

namespace Tests\Unit;

use App\MediaType\YoutubeVideo;
use App\Models\Media;
use App\Services\Discovery\SimilarTracks;
use App\Services\Discovery\YoutubeTrackResolver;
use App\Services\SpotifyAPI;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use SpotifyWebAPI\SpotifyWebAPI;
use Tests\CreatesApplication;

class SimilarTracksTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        Cache::flush();
    }

    protected function tearDown(): void
    {
        (new \ReflectionProperty(SpotifyAPI::class, 'api'))->setValue(null, null);
        parent::tearDown();
    }

    private function media(): Media
    {
        $media = Mockery::mock(Media::class)->makePartial();
        $media->id = 123;
        $media->shouldReceive('getOrFindSpotifyId')->andReturn('seed');

        return $media;
    }

    private function recommendations(array $limits): void
    {
        $api = Mockery::mock(SpotifyWebAPI::class);
        foreach ($limits as $limit) {
            $api->shouldReceive('getRecommendations')->once()
                ->with(['limit' => $limit, 'seed_tracks' => ['seed']])
                ->andReturn((object) ['tracks' => [(object) ['id' => 'recommended']]]);
        }
        (new \ReflectionProperty(SpotifyAPI::class, 'api'))->setValue(null, $api);
    }

    public function test_cache_hit_skips_providers_and_limits_have_separate_entries(): void
    {
        $this->recommendations([6, 24]);
        $video = new YoutubeVideo;
        $resolver = Mockery::mock(YoutubeTrackResolver::class);
        $resolver->shouldReceive('resolve')->twice()->andReturn([$video]);
        $this->app->instance(YoutubeTrackResolver::class, $resolver);
        $media = $this->media();

        $this->assertSame([$video], SimilarTracks::similarTracksByMedia($media, 6));
        $this->assertSame([$video], SimilarTracks::similarTracksByMedia($media, 6));
        $this->assertSame([$video], SimilarTracks::similarTracksByMedia($media, 24));
    }

    public function test_rechecks_cache_after_acquiring_lock(): void
    {
        $lock = Mockery::mock(Lock::class);
        Cache::shouldReceive('get')->twice()->with('similar_tracks:v2:123:24')->andReturn(null, []);
        Cache::shouldReceive('lock')->once()->with('similar_tracks:v2:123:24:lock', 1200)->andReturn($lock);
        $lock->shouldReceive('block')->once()->with(65, Mockery::type('Closure'))
            ->andReturnUsing(function ($seconds, $callback) {
                return $callback();
            });
        $media = new Media;
        $media->id = 123;
        // No Spotify ID or resolver is configured: either call would fail.
        $this->assertSame([], SimilarTracks::similarTracksByMedia($media));
    }

    public function test_empty_success_is_cached(): void
    {
        $this->recommendations([24]);
        $resolver = Mockery::mock(YoutubeTrackResolver::class);
        $resolver->shouldReceive('resolve')->once()->andReturn([]);
        $this->app->instance(YoutubeTrackResolver::class, $resolver);
        $media = $this->media();

        $this->assertSame([], SimilarTracks::similarTracksByMedia($media));
        $this->assertSame([], SimilarTracks::similarTracksByMedia($media));
    }

    public function test_provider_failure_releases_lock_and_does_not_cache_partial_results(): void
    {
        $this->recommendations([24, 24]);
        $resolver = Mockery::mock(YoutubeTrackResolver::class);
        $resolver->shouldReceive('resolve')->once()->andThrow(new \RuntimeException('provider unavailable'));
        $resolver->shouldReceive('resolve')->once()->andReturn([]);
        $this->app->instance(YoutubeTrackResolver::class, $resolver);
        $media = $this->media();

        try {
            SimilarTracks::similarTracksByMedia($media);
            $this->fail('Expected provider failure');
        } catch (\RuntimeException $e) {
            $this->assertSame('provider unavailable', $e->getMessage());
        }
        $this->assertSame([], SimilarTracks::similarTracksByMedia($media));
    }
}
