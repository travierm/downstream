<?php

namespace App\MediaType;

use Illuminate\Support\Str;
use App\MediaType\Transformer\YoutubeSearchResult;

class YoutubeVideo {
    public int $mediaId = 0;
    public bool $collected = false;
    public string $title;
    public string $videoId;
    public string $thumbnail;
    
    public static function createFromSearchResult($raw)
    {
        $data = YoutubeSearchResult::transform($raw);

        $youtubeVideo = new self();
        $youtubeVideo->title = html_entity_decode(htmlspecialchars_decode($data['title']), ENT_QUOTES, 'UTF-8');
        $youtubeVideo->videoId = $data['videoId'];
        $youtubeVideo->thumbnail = $data['thumbnail'];
        $youtubeVideo->guid = "guid_" . Str::random(35);

        return $youtubeVideo;
    }
}
?>