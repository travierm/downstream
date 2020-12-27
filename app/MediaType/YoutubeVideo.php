<?php

namespace App\MediaType;

use App\MediaType\Transformer\YoutubeSearchResult;

class YoutubeVideo {
    public int $mediaId;
    public bool $collected;

    public string $title;
    public string $videoId;
    public string $thumbnail;
    
    public static function createFromSearchResult($raw)
    {
        $data = YoutubeSearchResult::transform($raw);

        $youtubeVideo = new self();
        $youtubeVideo->title = $data['title'];
        $youtubeVideo->videoId = $data['videoId'];
        $youtubeVideo->thumbnail = $data['thumbnail'];

        return $youtubeVideo;
    }
}
?>