<?php
namespace App\Media;

use Cache;
use App\Media;
use App\UserMedia;
use YouTubeService;

class YouTubeV2 {

  public static function getInfo($index) 
  {
    return YouTubeService::getVideoInfo($index);
  }
  
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
    if(@$video->snippet->thumbnails->standard->url) {
      $meta['thumbnail'] = @$video->snippet->thumbnails->standard->url;
    }else{
      $meta['thumbnail'] = @$video->snippet->thumbnails->high->url;
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

  public static function searchFirst($query)
  {
    $results = self::search($query, 1);

    if(!$results) {
      return false;
    }

    if(count($results) >= 1) {
      $video = $results[0];
      $video->info = self::cleanInfo(self::getInfo($video->vid));

      return $video;
    }

    return false;
  }

  /**
   * Discover action made by user, import a video into the main media table
   * 
   * @param integer $userId
   * @param string $videoId
   * @param array $meta
   */
  public static function discover($userId, $videoId, $meta = [])
  {
    $video = YouTubeService::getVideoInfo($videoId);
    if(!$video) {
      return false;
    }

    $meta['title'] = $video->snippet->title;
    $meta['view_count'] = $video->statistics->viewCount;

    //thumbnail
    if(@$video->snippet->thumbnails->standard->url) {
      $meta['thumbnail'] = @$video->snippet->thumbnails->standard->url;
    }else{
      $meta['thumbnail'] = @$video->snippet->thumbnails->high->url;
    }

    $meta['categoryId'] = $video->snippet->categoryId;
    if(@$video->snippet->tags) {
      $meta['tags'] = $video->snippet->tags;
    }
    $meta = json_encode($meta);

    $media = Media::firstOrCreate([
      'origin' => 'youtube#search',
      'type' => 'youtube',
      'subtype' => 'video',
      'index' => $videoId,
      'user_id' => $userId,
      'meta' => $meta
    ]);

    $userMedia = UserMedia::firstOrCreate([
      'media_id' => $media->id,
      'user_id' => $userId
    ]);

    return true;
  }

  /** 
   * Search YouTube using API service
   * @param string $query 
   * @param integer $limit
  */
  public static function search($query, $limit = 8)
  {

    $limit +=1; 

    $results = YouTubeService::search($query, $limit);
    if(!$results) {
      return false;
    }

    $results = collect($results);
    $results = $results->filter(function($row) {
      return (!@is_null($row->id->videoId));
    });

    /*$videoIds = array_map(function($row) {
      return $row->id->videoId;
    }, $results->all());*/

    return self::cleanSearchResults($results->all());
  }

  /**
   * Search YouTube for video using query, Uses searchVideo instead of search api method
   * @param string $query 
   * @param integer $limit
   */
  public static function searchVideos($query, $limit)
  {
    $limit += 1;

    $results = YouTubeService::searchVideos($query, $limit);
    if(!$results) {
      return false;
    }

    $results = collect($results);
    $results = $results->filter(function($row) {
      return (!@is_null($row->id->videoId));
    });

    /*$videoIds = array_map(function($row) {
      return $row->id->videoId;
    }, $results->all());*/

    return self::cleanSearchResults($results->all());
  }

  public static function cleanInfo($info)
  {
    $return = new \stdClass();

    $return->title = $info->snippet->title;

    if(@$info->snippet->thumbnails->standard->url) {
      $return->thumbnail = @$info->snippet->thumbnails->standard->url;
    }else{
      $return->thumbnail = @$info->snippet->thumbnails->high->url;
    }

    return $return;
  }

  private static function cleanSearchResults($response)
  {
    $results = [];
    foreach($response as $video)
    {
      $vid = $video->id->videoId;
      $result = new \stdClass();
      $media = self::findByIndex($vid);

      $result->imported = false;
      $result->collected = false;
      $result->vid = $vid;
      $result->title = $video->snippet->title;
      if($media) {
        $result->imported = true;
        $result->collected = UserMedia::didCollect($media->id);
        $result->id = $media->id;
      }else{
        $result->id = false;
      }

      if(@$video->snippet->thumbnails->standard->url) {
        $result->thumbnail = @$video->snippet->thumbnails->standard->url;
      }else{
        $result->thumbnail = @$video->snippet->thumbnails->high->url;
      }

      $results[] = $result;
    }

    return $results;
  }

  private static function findByIndex($index)
  {
    return Media::findByType('youtube', $index)->first();
  }
}

?>
