<?php

namespace App\Http\Controllers;

use Cache;
use App\Media;
use App\Media\YouTubeV2;
use Illuminate\Http\Request;

class VideoManagerController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function getBrokenVideos() {

  	$ids = YouTubeV2::getBrokenVideoIds();
    $videos = Media::whereIn('index', $ids)->get();

  	return view('admin.video-manager', [
  		'videos' => $videos
  	]);
  }
}

?>