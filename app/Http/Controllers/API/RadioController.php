<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Media\YouTubeV2;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RadioController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function getSeedResults(Request $request)
    {
        $query = $request->input('query');

        $results = YouTubeV2::searchVideos($query, 30);

        return $results;
    }
}
