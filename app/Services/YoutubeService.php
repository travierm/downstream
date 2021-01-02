<?php
namespace App\Services;

use App\Models\Media;
use App\Models\UserMedia;

use App\MediaType\YoutubeVideo;
use Madcoda\Youtube\Facades\Youtube;

class YoutubeService {
    public static function getVideoById($id)
    {
        $result = Youtube::getVideoInfo($id);

        return YoutubeVideo::createFromSearchResult($result);
    }

    public static function updateCollectedOnVideos($videos, $userId) {
        $collectedMediaIds = UserMedia::where('user_id', $userId)->pluck('media_id');
        $collectedVideoIndexes = Media::whereIn('id', $collectedMediaIds)->pluck('index')->all();

        $updatedVideos = [];
        foreach($videos as $video) {
            $video->collected = in_array($video->videoId, $collectedVideoIndexes);

            $updatedVideos[] = $video;
        }

        return $updatedVideos;
    }

    public static function searchByQuery($query, $maxResults = 12)
    {
        $results = Youtube::searchAdvanced([
            'q' => $query,
            'type' => 'video',
            'videoCategoryId' => 10, // Search for music only
            'part' => 'id,snippet',
            'maxResults' => $maxResults
        ]);

        if(count($results) <= 0) {
            return [];
        }

        $videos = [];
        foreach($results as $raw) {
            $video = YoutubeVideo::createFromSearchResult($raw);
            $videos[] = $video;
        }

        return $videos;
    }
}

?>