<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserLike;
use App\YouTubeVideo;

class AppController extends Controller
{
  public function getIndex()
  {
    $videos = YouTubeVideo::all();

    foreach($videos as $video) {
      if(UserLike::didLikeVideo($video->id)) {
        $video->wasLiked = true;
      }else{
        $video->wasLiked = false;
      }
    }

    return view('index', [
      'videos' => $videos
    ]);
  }
}
