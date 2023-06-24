<?php

namespace App\Services\Discovery;

use Carbon\Carbon;
use App\Models\Media;
use App\MediaType\YoutubeVideo;
use App\Services\YoutubeService;
use Illuminate\Support\Facades\Cache;
use App\Services\Sources\SpotifyTrack;
use App\MediaType\Transformer\SpotifyTrackTransformer;
use App\Services\CodeBenchmark;
use Cumulati\Monolog\LogContext;

class SimilarTracks
{
    /**
     * @param Media $media
     * @param integer $limit
     * @return array<YoutubeVideo>
     */
    public static function similarTracksByMedia(Media $media, int $limit = 24): array
    {
        $benchmark = new CodeBenchmark();
        $benchmark->start();
        $lc = new LogContext(['media_id' => $media->id, 'limit' => $limit]);
        $lc->info('fetching similar tracks');

        $cacheKey = sprintf('similar_tracks_cache_for_%s', $media->id);

        /** @var array<YoutubeVideo> $similarTracks */
        $similarTracks = Cache::get($cacheKey, []);
        if (count($similarTracks) >= 1) {
            $lc->info('found similar tracks in cache', ['count' => count($similarTracks)]);

            return $similarTracks;
        }

        // Get Spotify ID of Track
        $spotifyId = $media->getOrFindSpotifyId();

        if (! $spotifyId) {
            $lc->warning('could not find spotify_id for media');
            throw new \Exception('Could not match video_id to discovery services');
        }

        // Use given track as a seed to find similar tracks on Spotify
        $seedTracks = SpotifyTrack::getSeedTracksByIds([$spotifyId], $limit);
        foreach ($seedTracks as $track) {
            // Convert Spotify results to array of data
            $trackData = SpotifyTrackTransformer::transform($track);

            // Search for YouTube Video by title of Spotify Track
            $youtubeSearchResults = YouTubeService::searchByQuery($trackData['title'], 1);
            if ($youtubeSearchResults[0]) {
                // Add first search result to the array of similar tracks
                $similarTracks[] = $youtubeSearchResults[0];
            }
        }
        $lc->info(sprintf('done fetching data from spotify and youtube', count($similarTracks)), [
            'seed_tracks' => count($seedTracks),
            'youtube_results' => count($similarTracks),
            'exec_time' => $benchmark->end(),
        ]);

        Cache::put($cacheKey, $similarTracks, Carbon::now()->addDay());

        return $similarTracks;
    }
}
