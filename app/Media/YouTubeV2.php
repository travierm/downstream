<?php
namespace App\Media;

use Cache;
use App\Media;
use YouTubeService;

class YouTubeV2 {
  public static function getBrokenVideoIds()
  {
    $cachedItems = Cache::get('youtube.brokenVideoIds');
    if($cachedItems) {
      return $cachedItems;
    }

    $videos = Media::all();

    $ids = [];
    foreach($videos as $video) {
      $test = YouTubeService::getVideoInfo($video->index);

      if(!$test) {
        $ids[] = $video->index;
      }
    }

    $expiresAt = now()->addDays(1);
    Cache::put('youtube.brokenVideoIds', $ids, $expiresAt);

    return $ids;
  }

  public static function updateMedia($mediaId, $videoId)
  {
    $video = YouTubeService::getVideoInfo($videoId);
    $media = Media::find($mediaId);

    if(!$video or !$media) {
      return false;
    }

    //Create Meta data array
    $meta = [];
    $meta['title'] = $video->snippet->title;
    $meta['view_count'] = $video->statistics->viewCount;

    //thumbnail
    if($video->snippet->thumbnails->standard->url) {
      $meta['thumbnail'] = @$video->snippet->thumbnails->standard->url;
    }else{
      $meta['thumbnail'] = @$video->snippet->thumbnails->default->url;
    }

    $meta['categoryId'] = $video->snippet->categoryId;
    if(@$video->snippet->tags) {
      $meta['tags'] = $video->snippet->tags;
    }
    $meta = json_encode($meta);

    $media->index = $videoId;
    $media->meta = $meta;
    $media->save();

    return true;
  }
}

?>
