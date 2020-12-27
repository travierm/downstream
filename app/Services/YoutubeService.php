<?php
namespace App\Services;

use YoutubeAPI;
use App\MediaType\YoutubeVideo;

class YoutubeService {
    public static function getVideoById($id)
    {
        $result = YoutubeAPI::getVideoInfo($id);

        return YoutubeVideo::createFromSearchResult($result);
    }

    public static function searchByQuery($query, $maxResults = 20)
    {
        $results = YoutubeAPI::searchAdvanced([
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