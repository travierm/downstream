<?php

namespace App\Http\Controllers;

use App\Media;
use App\UserMedia;
use Illuminate\Http\Request;

class TheaterController extends Controller
{
  public $queueLength = 30;
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function queueFromMediaId(Request $req)
  {
    if(!$req->mediaId) {
      return false;
    }
    $mediaId = $req->mediaId;

    //look for media in user collection first
    $videos = UserMedia::collection('youtube');

    $didHitId = false;
    $queue = $videos->map(function($video) use($mediaId) {
      if($video->media_id = $mediaId) {
        $didHitId = true;
      }

      if($didHitId) {
        $video = Media::byType('youtube')
          ->where('id', $video->media_id)
          ->limit(30)
          ->first();
        $video->collected = UserMedia::didCollect($video->id);
      }

      return $video;
    });

    if($queue->count() <= 20) {
      //how far from queue length
      $distance = $this->queueLength - $queue->count();
      $frontPageVideos = Media::byType('youtube')
        ->limit($distance)
        ->orderByRaw('created_at DESC')
        ->get();

      Media::addUserCollectedProp($frontPageVideos);

      $videos = $videos->concat($frontPageVideos);
    }

    return $videos->all();
  }
}
