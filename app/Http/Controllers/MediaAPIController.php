<?php

namespace App\Http\Controllers;

use Auth;
use Route;
use App\MediaResolver;
use Illuminate\Http\Request;

class MediaAPIController extends Controller
{
  public $types = [
    "youtube" => [
      //sub type
      'video',
      'live',
      'playlist'
    ],
    'soundcloud' => [
      'track'
    ]
  ];
  private $userId = false;

  public function __construct()
  {
    //$this->middleware('auth:api');


    $this->middleware(function ($request, $next) {
           $this->userId = Auth::user()->id;

           return $next($request);
    });


    MediaResolver::init($this->userId);
  }

  public function resolve(Request $request)
  {

      $uri = Route::current()->uri;
      $action = $this->getURIAction($uri);
      $type = $request->type;

      if(!$type or !$action) {
        return response()->json([
          'code'      =>  401,
          'message'   =>  "Invalid query parameters action or type"
        ], 401);
      }


      return MediaResolver::dispatch($type, $action, $request->input());
  }

  private function getURIAction($uri)
  {
    $routeVars = explode("/", $uri);

    if(count($routeVars) <= 2) {
      return false;
    }

    return $routeVars[2];
  }
}
