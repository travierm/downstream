<?php

namespace App\Services\Discovery;

use App\MediaType\YoutubeVideo;
use App\Models\Media;
use App\Services\CodeBenchmark;
use App\Services\Sources\SpotifyTrack;
use Carbon\Carbon;
use Cumulati\Monolog\LogContext;
use Illuminate\Support\Facades\Cache;

class SimilarTracks
{
    /**
     * @return array<YoutubeVideo>
     */
    public static function similarTracksByMedia(Media $media, int $limit = 24): array
    {
        $benchmark = new CodeBenchmark;
        $benchmark->start();
        $lc = new LogContext(['media_id' => $media->id, 'limit' => $limit]);
        $lc->info('fetching similar tracks');

        $limit = max(1, min(100, $limit));
        $cacheKey = sprintf('similar_tracks:v2:%s:%s', $media->id, $limit);
        $similarTracks = Cache::get($cacheKey);
        if ($similarTracks !== null) {
            $lc->info('found similar tracks in cache', ['count' => count($similarTracks), 'exec_time' => $benchmark->end()]);

            return $similarTracks;
        }

        // All workers must use the same cache store. The lease exceeds the bounded
        // YouTube work (at most 100 searches, 10 seconds each at concurrency 1).
        return Cache::lock($cacheKey.':lock', 1200)->block(65, function () use ($cacheKey, $media, $limit, $lc, $benchmark) {
            // Another worker may have filled the cache while this request waited.
            $similarTracks = Cache::get($cacheKey);
            if ($similarTracks !== null) {
                return $similarTracks;
            }

            $spotifyId = $media->getOrFindSpotifyId();
            if (! $spotifyId) {
                throw new \RuntimeException('Could not match video_id to discovery services');
            }

            $seedTracks = SpotifyTrack::getSeedTracksByIds([$spotifyId], $limit);
            if ($seedTracks === false) {
                throw new \RuntimeException('Spotify discovery is temporarily unavailable. Please try again.');
            }
            $similarTracks = app(YoutubeTrackResolver::class)->resolve($seedTracks);
            $lc->info('done fetching data from spotify and youtube', [
                'seed_tracks' => count($seedTracks),
                'youtube_results' => count($similarTracks),
                'exec_time' => $benchmark->end(),
            ]);
            Cache::put($cacheKey, $similarTracks, count($similarTracks) ? Carbon::now()->addDay() : Carbon::now()->addMinutes(5));

            return $similarTracks;
        });
    }
}
