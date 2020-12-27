<?php

namespace App\Http\Controllers\API;

use App\Services\YoutubeService;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function getResults($query)
    {
        $results = YoutubeService::searchByQuery($query);

        return response()->json([
            'results' => $results
        ], 200);
    }
}
