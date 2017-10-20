<?php

namespace App\Http\Controllers;

use Youtube;
use Auth;
use App\UserLike;
use App\YouTubeVideo;

use Illuminate\Http\Request;


class ImportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function getIndex()
  {

    return view('import.index');
  }

  public function attemptYouTubeImport($userId, $vid)
  {
    $vid = $this->cleanVid($vid);

    if(!$vid) {
      return [false, "Video ID could not be found."];
    }

    $apiInfo = YouTube::getVideoInfo($vid);
    if(!$apiInfo) {
      return [false, "Could not find video on YouTube."];
    }

    if(YouTubeVideo::isDuplicate($vid)) {
      return [true, "Already imported"];
    }

    $video = new YouTubeVideo;
    $video->vid = $vid;
    $video->user_id = $userId;
    $video->title = $apiInfo->snippet->title;
    $video->description = $apiInfo->snippet->description;
    if(@$apiInfo->snippet->tags) {
      $video->tags = json_encode($apiInfo->snippet->tags);
    }
    $video->view_count = $apiInfo->statistics->viewCount;
    $status = $video->save();

    if(!$status) {
      return [false, "Database error."];
    }else{
      return [true, "Imported video successfully"];
    }
  }

  public function postImportVideo(Request $req)
  {
    $vid = $this->cleanVid($req->input('vid'));

    if(!$vid) {
      return redirect()->back()->withErrors("Video ID could not be found!");
    }

    $apiInfo = Youtube::getVideoInfo($vid);
    if(!$apiInfo) {
      return redirect()->back()->withErrors("Could not find video on YouTube!");
    }

    $video = new YouTubeVideo;
    $video->vid = $vid;
    $video->user_id = Auth::user()->id;
    $video->title = $apiInfo->snippet->title;
    $video->description = $apiInfo->snippet->description;
    if(@$apiInfo->snippet->tags) {
      $video->tags = json_encode($apiInfo->snippet->tags);
    }
    $video->view_count = $apiInfo->statistics->viewCount;

    $video->save();
    if($video->id) {
      $like = new UserLike;
      $like->user_id = Auth::user()->id;
      $like->table = "youtube_videos";
      $like->index = $video->id;
      $like->save();
    }

    return redirect()->back()->with('success', true);
  }

  private function cleanVid($data)
  {
    if($this->isYouTubeURL($data)) {
      return $this->vidFromYouTubeURL($data);
    }

    return $data;
  }

  private function isYouTubeURL($url)
  {
    if(strpos($url, 'watch?') !== false) {
      return true;
    }

    return false;
  }

  private function vidFromYouTubeURL($url)
  {
    return substr($url, strpos($url, "=") + 1);
  }
}
