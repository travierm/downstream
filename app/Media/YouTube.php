<?php
namespace App\Media;

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

  public function collect()
  {

  }

  public function toss()
  {

  }

  //Add to media library and add to user collectables
  public function discover($videoId)
  {
    $video = YouTubeService::getVideoInfo($videoId);
    if(!$video) {
      return false;
    }

    $meta = [];
    $meta['title'] = $video->snippet->title;
    $meta['view_count'] = $video->statistics->viewCount;
    $meta['thumbnail'] = $video->snippet->thumbnails->standard->url;
    $meta['categoryId'] = $video->snippet->categoryId;
    if(@$video->snippet->tags) {
      $meta['tags'] = $video->snippet->tags;
    }

    $meta = json_encode($meta);

    $media = new Media();
    $media->origin = 'youtube#search';
    $media->type = 'youtube';
    $media->subtype = 'video';
    $media->index = $videoId;
    $media->user_id = $this->userId;
    $media->meta = $meta;
    $media->save();

    $userMedia = new UserMedia();
    $userMedia->media_id = $media->id;
    $userMedia->user_id = $this->userId;
    $userMedia->save();

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
        $result->id = uniqid();
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
