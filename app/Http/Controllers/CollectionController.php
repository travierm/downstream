<?php

namespace App\Http\Controllers;

use App\UserCollection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function getIndex()
  {
    $videos = UserCollection::getYouTubeVideos();

    return view('collection.index', [
      'videos' => $videos
    ]);
  }
}
