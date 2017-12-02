<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;

class FrontPageAPIController extends Controller
{

  public function __construct()
  {
    //$this->middleware('auth:api');
  }

  public function index(Request $req)
  {
    $videos = Media::byType('youtube')
      ->limit(25)
      ->orderByRaw('created_at DESC')
      ->get();

    //$videos = Media::addUserCollectedProp($videos);

    return response()->json([
      'code' =>  200,
      'videos' => $videos
    ], 200);
  }
}
