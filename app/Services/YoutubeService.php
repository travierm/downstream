<?php
namespace App\Services;

use App\MediaType\YoutubeVideo;
use Madcoda\Youtube\Facades\Youtube;
class YoutubeService {
    public static function getVideoById($id)
    {
        $result = Youtube::getVideoInfo($id);

        return YoutubeVideo::createFromSearchResult($result);
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