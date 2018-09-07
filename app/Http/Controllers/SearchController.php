<?php

namespace App\Http\Controllers;

use Auth;
use App\Media\YouTubeV2;
use Illuminate\Http\Request;

class SearchController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function getIndex(Request $req) {
    if($req->input('query')) {
      return $this->getSearchYouTube($req);
    }

    return view('search.index');
  }

  public function getSearchYouTube(Request $req)
  {
    $query = $req->input('query');

    $limit = 17;
    $results = YouTubeV2::search($query, $limit);


    return view('search.index')->with([
      'query' => $req->input('query'),
      'videos' => $results,
      'success' => true
    ]);
  }
}
