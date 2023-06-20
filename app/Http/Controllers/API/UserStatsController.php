<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repos\UserStatsRepo;
use Illuminate\Http\Request;

class UserStatsController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();
        $userStatsRepo = new UserStatsRepo();

        $stats = [
            'top_ten_tracks' => $userStatsRepo->getTopPlayedTracks($user, 10),
            'play_count_history' => $userStatsRepo->getPlayCountHistory($user, 9),
            'collection_count_history' => $userStatsRepo->getCollectionCountHistory($user, 9),
        ];

        return response()->json($stats);
    }
}
