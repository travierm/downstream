<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use YouTubeService;
use App\YouTubeVideo;
use App\MediaResolver;

class SearchController extends Controller
{
  private $userId = false;
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
       $this->userId = Auth::user()->id;

       return $next($request);
    });

    $this->resolver = new MediaResolver($this->userId);
  }

  public function getIndex() {
    return view('search.index');
  }

  public function postSearchYouTube(Request $req)
  {
    $results = $this->resolver->dispatch('youtube', 'search', [
      'query' => $req->input('query')
    ]);

    dd($results);

    return view('search.index')->with([
      'query' => $req->input('query'),
      'videos' => $videos,
      'success' => true
    ]);
  }
}
