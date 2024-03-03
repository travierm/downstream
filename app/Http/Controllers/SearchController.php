<?php

namespace App\Http\Controllers;

use View;
use Auth;
use App\Services\FilterMedia;
use App\Models\Media\YouTubeV2;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getIndex(Request $req)
    {
        if($req->input('ds_query')) {
            return $this->getSearchYouTube($req);
        }

        return view('search.index');
    }

    public function getSearchYouTube(Request $req)
    {
        $query = $req->input('ds_query');

        $limit = 17;
        $results = YouTubeV2::search($query, $limit);

        foreach($results as &$result) {
            $result->title = FilterMedia::title($result->title);
        }

        View::share('searchQuery', $query);

        return view('search.index')->with([
          'videos' => $results,
          'success' => true
        ]);
    }
}
