<?php

namespace App\Http\Controllers\API;

use App\Media\YouTubeV2;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DemoController extends Controller
{
    public function searchQuery(Request $req)
    {
      $query = $req->input('query');
  
      $limit = 7;
      $results = YouTubeV2::search($query, $limit);

      return $results;
    }
}
