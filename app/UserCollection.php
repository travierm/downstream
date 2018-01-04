<?php

namespace App;

use Auth;
use App\YouTubeVideo;
use Illuminate\Database\Eloquent\Model;

class UserCollection extends Model
{
  protected $table = "user_collections";

  public static function getYouTubeCollectable($id)
  {
    $userId = Auth::user()->id;

    $collectable = UserCollection::where('user_id', $userId)
      ->where('table', 'youtube_videos')
      ->where('index', $id)
      ->orderBy('created_at', 'DESC')
      ->first();
      
    return $collectable;
  }

  public static function didLikeVideo($id)
  {
    $videoLike = self::where('user_id', Auth::user()->id)
      ->where('table', 'youtube_videos')
      ->where('index', $id)
      ->exists();

    return $videoLike;
  }

  public static function getYouTubeVideos()
  {
    $videoIndexes = self::where('user_id', Auth::user()->id)
      ->where('table', 'youtube_videos')
      ->pluck('index');

    $videos = YouTubeVideo::whereIn('id', $videoIndexes)->get();
    $videos->map(function($video) {
      $video->collected = true;
      return $video;
    });

    return $videos;
  }
}
