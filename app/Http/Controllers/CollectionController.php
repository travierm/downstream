<?php

namespace App\Http\Controllers;

use App\UserLike;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function getIndex()
  {
    $videos = UserLike::getYouTubeVideos();

    return view('collection.index', [
      'videos' => $videos
    ]);
  }
}
