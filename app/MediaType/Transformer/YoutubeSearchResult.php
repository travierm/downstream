<?php

namespace App\MediaType\Transformer;

class YoutubeSearchResult
{
    public static function transform(object $raw)
    {
        $data = [];
        $snippet = $raw->snippet;

        $thumbnail = '';

        // High Quality Thumbnail
        if (@$snippet->thumbnails->high) {
            $thumbnail = $snippet->thumbnails->high->url;

        // Medium Quality Thumbnail
        } elseif (@$snippet->thumbnails->medium) {
            $thumbnail = $snippet->thumbnails->medium->url;

        // Default Quality Thumbnail
        } elseif (@$snippet->thumbnails->default) {
            $thumbnail = $snippet->thumbnails->default->url;
        }

        $data['title'] = $snippet->title;
        $data['videoId'] = (@$raw->id->videoId ?? $raw->id);
        $data['thumbnail'] = $thumbnail;

        return $data;
    }
}
