<?php
namespace App\Media;

use DB;
use App\Media;
use App\UserMedia;
use YouTubeService;

class YouTube {
  public $typeName = "youtube";

  private $userId = null;

  public function __construct($userId)
  {
    $this->userId = $userId;
  }

  //Add to media library
  private function add()
  {
    return [1, 2, 3];
  }

  //Remove from library
  private function remove()
  {

  }

  public function collection()
  {
    $mediaIds = UserMedia::where('user_id', $this->userId)
      ->orderBy('id', 'DESC')
      ->pluck('media_id');

    $collection = [];
    foreach($mediaIds as $id) {
      $media = Media::find($id);
      $media->meta = json_decode($media->meta);
      $collection[] = $media;
    }

    $collection = Media::addUserCollectedProp($collection);

    return $collection;
  }

  /**
   * Toss media from UserMedia
   * Media is still kept with user_id attached
   * @return [type] [description]
   */
  public function toss($mediaId)
  {
    $userMedia = UserMedia::findById($mediaId, $this->userId);
    if($userMedia) {
      $userMedia->delete();
    }

    return true;
  }

  //Add to media library and add to user collectables
  public function discover($videoId)
  {
    $video = YouTubeService::getVideoInfo($videoId);
    if(!$video) {
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

    $media = Media::returnOrCreate([
      'origin' => 'youtube#search',
      'type' => 'youtube',
      'subtype' => 'video',
      'index' => $videoId,
      'user_id' => $this->userId,
      'meta' => $meta
    ]);

    $userMedia = UserMedia::firstOrCreate([
      'media_id' => $media->id,
      'user_id' => $this->userId
    ]);

    return true;
  }

  public function search($query)
  {

    $results = YouTubeService::search($query, 10);
    if(!$results) {
      return false;
    }

    $results = collect($results);
    $results = $results->filter(function($row) {
      return (!@is_null($row->id->videoId));
    });

    $videoIds = array_map(function($row) {
      return $row->id->videoId;
    }, $results->all());

    return $this->cleanSearchResults($videoIds);
  }

  private function cleanSearchResults($videoIds)
  {
    $results = [];
    foreach($videoIds as $vid)
    {
      $result = new \stdClass();
      $media = self::findByIndex($vid);

      $result->imported = false;
      $result->collected = false;
      $result->vid = $vid;
      if($media) {
        $result->imported = true;
        $result->collected = UserMedia::didCollect($media->id);
        $result->id = $media->id;
      }else{
        $result->id = false;
      }

      $results[] = $result;
    }

    return $results;
  }

  private function findByIndex($index)
  {
    return Media::findByType('youtube', $index)->first();
  }


  private function isDuplicate($index)
  {
    return UserMedia::findByType('youtube', $index)->exists();
  }

  private function cleanVid($data)
  {
    if($this->isYouTubeURL($data)) {
      return $this->vidFromYouTubeURL($data);
    }

    return $data;
  }

  private function isYouTubeURL($url)
  {
    if(strpos($url, 'watch?') !== false) {
      return true;
    }

    return false;
  }

  private function vidFromYouTubeURL($url)
  {
    return substr($url, strpos($url, "=") + 1);
  }
}
?>
