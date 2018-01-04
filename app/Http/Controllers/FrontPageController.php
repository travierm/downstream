<?php

namespace App\Http\Controllers;

use Auth;
use App\Media;

use Illuminate\Http\Request;

class FrontPageController extends Controller
{
  public function index()
  {
    $videos = Media::byType('youtube')
      ->limit(10)
      ->orderByRaw('created_at DESC')
      ->get();

    if(Auth::check()) {
      $videos = Media::addUserCollectedProp($videos);
    }

    return view('frontpage.index', [
      'videos' => $videos
    ]);
  }
}
