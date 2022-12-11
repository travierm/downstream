<?php

namespace App\Services;

use App\MediaType\YoutubeVideo;
use App\Models\Media;
use App\Models\UserMedia;
use Madcoda\Youtube\Facades\Youtube;

class YoutubeService
{
    public static function getVideoById($id)
    {
        $result = Youtube::getVideoInfo($id);

        return YoutubeVideo::createFromSearchResult($result);
    }

    public static function updateMediaIdOnVideos($videos)
    {
        $videos = collect($videos);
        $matchedMediaItems = Media::whereIn('index', $videos->pluck('videoId'))->get();

        $updatedVideos = [];
        foreach ($videos as $video) {
            foreach ($matchedMediaItems as $media) {
                if ($media->index == $video->videoId) {
                    $video->mediaId = $media->id;
                }
            }

            $updatedVideos[] = $video;
        }

        return $updatedVideos;
    }

    public static function updateCollectedOnVideos($videos, $userId)
    {
        $collectedMediaIds = UserMedia::where('user_id', $userId)->pluck('media_id');
        $collectedVideoIndexes = Media::whereIn('id', $collectedMediaIds)->pluck('index')->all();

        $updatedVideos = [];
        foreach ($videos as $video) {
            $video->collected = in_array($video->videoId, $collectedVideoIndexes);

            $updatedVideos[] = $video;
        }

        return $updatedVideos;
    }

    public static function searchByQuery(string $query, int $maxResults = 12)
    {
        $results = Youtube::searchAdvanced([
            'q' => $query,
            'type' => 'video',
            'part' => 'id,snippet',
            'maxResults' => $maxResults,
            // 'videoCategoryId' => 10, // Search for music only
        ]);

        if (count($results) <= 0) {
            return [];
        }

        $videos = [];
        foreach ($results as $raw) {
            $video = YoutubeVideo::createFromSearchResult($raw);
            $videos[] = $video;
        }

        return $videos;
    }
}
