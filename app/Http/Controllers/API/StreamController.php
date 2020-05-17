<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Media\YouTubeV2;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StreamController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function postSearchResults(Request $request)
    {
        $query = $request->input('query');

        $results = YouTubeV2::searchWithDuration($query, 2);

        return $results;
    }
}
