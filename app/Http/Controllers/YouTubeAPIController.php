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

  }
}
