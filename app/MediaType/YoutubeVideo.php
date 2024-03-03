<?php

namespace App\Models\MediaType;

use App\Models\MediaType\Transformer\YoutubeSearchResult;
use App\Services\Filter;
use Illuminate\Support\Str;

class YoutubeVideo
{
    public int $mediaId = 0;

    public bool $collected = false;

    public string $title;

    public string $videoId;

    public string $thumbnail;

    public static function createFromSearchResult($raw): YoutubeVideo
    {
        $data = YoutubeSearchResult::transform($raw);

        $youtubeVideo = new self();
        $youtubeVideo->title = html_entity_decode(htmlspecialchars_decode($data['title']), ENT_QUOTES, 'UTF-8');
        $youtubeVideo->title = Filter::title($youtubeVideo->title);

        $youtubeVideo->videoId = $data['videoId'];
        $youtubeVideo->thumbnail = $data['thumbnail'];
        $youtubeVideo->guid = 'guid_'.Str::random(35);

        return $youtubeVideo;
    }
}
