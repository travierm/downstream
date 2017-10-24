<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use YouTube;
use App\YouTubeVideo;

class SearchController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function getIndex() {
    return view('search.index');
  }

  public function postSearchYouTube(Request $req)
  {
    $res = YouTube::search($req->input('query'), 10);
    if(!$res) {
      return redirect()->back()->withErrors("No results found!");
    }

    $results = collect($res);
    $results = $results->filter(function($row) {
      return (!@is_null($row->id->videoId));
    });

    $videoIds = array_map(function($row) {
      return $row->id->videoId;
    }, $results->all());

    $videos = YouTubeVideo::cleanSearchResults($videoIds);
    //dd($videos);

    return view('search.index')->with([
      'query' => $req->input('query'),
      'videos' => $videos,
      'success' => true
    ]);
  }
}
