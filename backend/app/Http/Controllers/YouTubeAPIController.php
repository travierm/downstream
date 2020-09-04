<?php

namespace App\Http\Controllers;

use Auth;
use App\Media\YouTube;
use YouTubeServiceService;
use App\UserCollection;
use App\YouTubeServiceVideo;
use Illuminate\Http\Request;

class YouTubeAPIController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function test() {
    return "test";
  }

  public function postSearchByQuery($req)
  {
    $query = $req->input('query');
    $results = $this->youtube->search($query);

    dd($results);
  }

  public function toss(Request $req)
  {
    $id = $req->input('id');

    $collectable = UserCollection::getYouTubeServiceCollectable($id);

    if($collectable) {
      $collectable->delete();
      return response()->json([
            'code'      =>  200,
            'message'   =>  "Tossed YouTubeService Video from collection"
        ], 200);
    }else{
      return response()->json([
            'code'      =>  200,
            'message'   =>  "Could not find collectable"
        ], 200);
    }
  }

  public function collect(Request $req)
  {
    $userId = Auth::guard('api')->user()->id;
    $vid = $req->input('vid');

    //returns either false for bad import or the id of the video
    $result = app('App\Http\Controllers\ImportController')->attemptYouTubeServiceImport($userId, $vid);
    if(!$result) {
      return response()->json([
            'code'      =>  401,
            'message'   =>  "Could not import"
        ], 401);
    }

    //database id of video not YouTubeService.video_id
    $videoId = $result;
    //check if user already liked this video
    if(UserCollection::didLikeVideo($videoId)) {
      return response()->json([
            'code'      =>  200,
            'message'   =>  "Already collected video"
        ], 200);
    }

    $collectable = new UserCollection;
    $collectable->user_id = $userId;
    $collectable->table = "YouTubeService_videos";
    $collectable->index = $videoId;
    $result = $collectable->save();

    if(!$result) {
      return response()->json([
            'code'      =>  401,
            'message'   =>  "Could not collected item"
        ], 401);
    }else{
      return response()->json([
            'code'      =>  200,
            'message'   =>  "Collected YouTubeService Video"
        ], 200);
    }
  }
}
