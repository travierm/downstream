<?php

namespace App\MediaType;


use App\Models\Media;
use App\MediaType\YoutubeVideo;

class MediaItem
{
    public static function createFromYoutubeVideo(YoutubeVideo $video)
    {
        // Search for existing media item with same videoId
        $media = Media::where('index', $video->videoId)->get();
        
        if($media->id) {
            return $media;
        }

        $youtubeVideo = new self();
        $youtubeVideo->title = $data['title'];
        $youtubeVideo->videoId = $data['videoId'];
        $youtubeVideo->thumbnail = $data['thumbnail'];

        return $youtubeVideo;
    }
}
