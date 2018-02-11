<?php

namespace App\Http\Controllers;

use Auth;
use App\Media\YouTube;
use Illuminate\Http\Request;

class SearchController extends Controller
{
  private $userId = false;
  public function __construct()
  {
    $this->middleware('auth:api');

    $this->middleware(function ($request, $next) {
      $this->userId = Auth::user()->id;
      $this->youtube = new YouTube($this->userId);

       return $next($request);
    });
  }

  public function getIndex() {
    return view('search.index');
  }

  public function postSearchYouTube(Request $req)
  {
    $query = $req->input('query');
    $results = $this->youtube->search($query);

    return view('search.index')->with([
      'query' => $req->input('query'),
      'videos' => $results,
      'success' => true
    ]);
  }
}
