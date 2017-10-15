<?php

namespace App;

use Auth;
use App\YouTubeVideo;
use Illuminate\Database\Eloquent\Model;

class UserCollection extends Model
{
  protected $table = "user_collections";

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

    return YouTubeVideo::whereIn('id', $videoIndexes)->get();
  }
}
