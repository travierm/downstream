<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserCollection ;
use App\YouTubeVideo;

class AppController extends Controller
{
  public function getIndex()
  {
    $videos = YouTubeVideo::all();

    return view('index', [
      'videos' => $videos
    ]);
  }
}
