<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class YouTubeVideo extends Model
{
  public $table = "youtube_videos";

  public static function getUserVideos()
  {
    return self::where('user_id', Auth::user()->id)->get();
  }
}
