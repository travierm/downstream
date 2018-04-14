<?php

namespace App\Http\Controllers;

use Auth;
use App\Media\YouTubeV2;
use Illuminate\Http\Request;

class SearchController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function getIndex() {
    return view('search.index');
  }

  public function postSearchYouTube(Request $req)
  {
    $query = $req->input('query');

    $limit = 8;
    $results = YouTubeV2::search($query, $limit);

    return view('search.index')->with([
      'query' => $req->input('query'),
      'videos' => $results,
      'success' => true
    ]);
  }
}
