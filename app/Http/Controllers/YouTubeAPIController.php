<?php

namespace App\Http\Controllers;

use Auth;
use YouTube;
use App\UserCollect;
use App\YouTubeVideo;
use Illuminate\Http\Request;

class YouTubeAPIController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function importAndCollect(Request $req)
  {
    $userId = Auth::guard('api')->user()->id;
    $vid = $req->input('vid');

    $result = app('App\Http\Controllers\ImportController')->attemptYouTubeImport($userId, $vid);
    dd($result);
  }
}
