<?php
namespace App\Media;

use DB;
use App\Media;
use App\User;
use App\UserMedia;
use YouTubeService;

class Video {
  public $id;
  public $title;
  public $thumbnail = "";
  public $tags = [];
  public $categoryId = "";

  public function mediaExists()
  {
    return Media::where('index', $this->id)
      ->where('type', 'youtube')
      ->exists();
  }

  public function allKeysFilled()
  {
    foreach($this as $key => $value) {
      if(!$value) {
        return false;
      }
    }

    return true;
  }
}

class YouTube {
  public $name = "youtube";

  public function search($query, $limit = 5)
  {
    $results = YouTubeService::search($query, $limit);

    if(!$results) {
      return false;
    }

    $videos = [];
    foreach($results as $info) {
      if(@$info->id->videoId) {
        $video = $this->getById($info->id->videoId);  

        $videos[] = $video;
      }
    }

    return $videos;
  }

  public function getById($id)
  {
    return $this->formatVideoInfo(YouTubeService::getVideoInfo($id));
  }

  /**
   * formatVideoInfo
   * 
   * format info given from YouTube API into Video object type
   * @param object $info 
   */
  private function formatVideoInfo($info)
  {
    $video = new Video();
    $video->id = $info->id;
    $video->title = $info->snippet->title;
    $video->categoryId = $info->snippet->categoryId;

    if($video->tags)
      $video->tags = $info->snippet->tags;

    if(@$info->snippet->thumbnails->standard->url) {
      $video->thumbnail = @$info->snippet->thumbnails->standard->url;
    }else{
      $video->thumbnail = @$info->snippet->thumbnails->high->url;
    }

    return $video;
  }
}
?>
