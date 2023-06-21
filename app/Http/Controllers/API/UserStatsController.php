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
            'top_played_tracks' => $userStatsRepo->getTopPlayedTracks($user, 24),
            'collection_count_history' => $userStatsRepo->getCollectionCountHistory($user, 48),
            'play_count_history' => $userStatsRepo->getPlayCountHistory($user, 9),
            'play_count_history_by_days' => $userStatsRepo->getPlayCountHistoryByDays($user, 9),
        ];

        return response()->json($stats);
    }
}
