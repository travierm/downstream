<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Youtube;

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
    $res = Youtube::search($req->input('query'), 10);
    if(!$res) {
      return redirect()->back()->withErrors("No results found!");
    }

    $results = collect($res);
    $results = $results->filter(function($row) {
      return (!@is_null($row->id->videoId));
    });

    $videoIds = $results->keyBy(function($row) {
      return $row->id->videoId;
    });

    return view('search.index')->with([
      'query' => $req->input('query'),
      'videos' => $videoIds->all(),
      'success' => true
    ]);
  }
}
