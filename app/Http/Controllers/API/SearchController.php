<?php

namespace App\Http\Controllers\API;

use App\Services\YoutubeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function getResults($query)
    {
        $lc = getRequestLogContext();

        $userId = Auth::user()->id;
        $lc->info('search request started', [
            'query' => $query
        ]);

        $videos = YoutubeService::searchByQuery($query);
        $videos = YoutubeService::updateMediaIdOnVideos($videos);
        $videos = YoutubeService::updateCollectedOnVideos($videos, $userId);

        $lc->info(sprintf('found %s videos results', count($videos)));

        return response()->json([
            'results' => $videos
        ], 200);
    }
}
