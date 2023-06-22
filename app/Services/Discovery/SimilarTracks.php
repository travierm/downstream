<?php

namespace App\Services\Discovery;

use App\MediaType\Transformer\SpotifyTrackTransformer;
use App\Models\Media;
use App\Services\Sources\SpotifyTrack;
use App\Services\YoutubeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SimilarTracks
{
    public static function similarTracksByMedia(Media $media, int $limit = 24)
    {
        $cacheKey = sprintf('similar_tracks_cache_for_%s', $media->id);

        $similarTracks = Cache::get($cacheKey, []);
        if (count($similarTracks) >= 1) {
            return $similarTracks;
        }

        // Get Spotify ID of Track
        $spotifyId = $media->getOrFindSpotifyId();

        if (! $spotifyId) {
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

        Cache::put($cacheKey, $similarTracks, Carbon::now()->addDay());

        return $similarTracks;
    }
}
