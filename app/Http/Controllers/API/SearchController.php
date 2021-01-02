<?php

namespace App\Http\Controllers\API;

use App\Services\YoutubeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function getResults($query)
    {
        $userId = Auth::user()->id;

        $videos = YoutubeService::searchByQuery($query);
        $videos = YoutubeService::updateMediaIdOnVideos($videos);
        $videos = YoutubeService::updateCollectedOnVideos($videos, $userId);

        return response()->json([
            'results' => $videos
        ], 200);
    }
}
