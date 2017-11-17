<?php
namespace App\Media;

use Media;
use UserMedia;
use YouTubeService;

class YouTube {
  public $typeName = "youtube";

  private $userId = null;

  public function __construct($userId)
  {
    $this->userId = $userId;
  }

  //Add to media library
  public function add()
  {
    return [1, 2, 3];
  }

  //Remove from library
  public function remove()
  {

  }

  //Add to media library and add to user collectables
  public function discover()
  {

  }

  public function search($query, $test2)
  {
    dd($query);

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

    dd($videoIds);
  }

  private function cleanSearchResults($videoIds)
  {
    $results = [];
    foreach($videoIds as $vid)
    {
      $result = new \stdClass();
      $video = self::findByVID($vid);

      $result->imported = false;
      $result->collected = false;
      $result->vid = $vid;
      if($video) {
        $result->imported = true;
        $result->collected = UserCollection::didLikeVideo($video->id);
        $result->id = $video->id;
      }else{
        $result->id = uniqid();
      }

      $results[] = $result;
    }

    return $results;
  }

  private static function findByVID($vid)
  {
    return self::where('vid', $vid)->first();
  }

  private static function isDuplicate($vid)
  {
    return self::where('vid', $vid)->exists();
  }

  private static function getUserVideos()
  {
    return self::where('user_id', Auth::user()->id)->get();
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
