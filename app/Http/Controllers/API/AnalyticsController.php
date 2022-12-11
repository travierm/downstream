<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserMediaPlays;
use Auth;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function getStats()
    {
        $playsToday = UserMediaPlays::whereDate('created_at', Carbon::today())->count();
        $playsThisWeek = UserMediaPlays::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();
        $playsThisMonth = UserMediaPlays::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();
        $playsThisYear = UserMediaPlays::whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
            ->count();

        return response()->json([
            'plays' => [
                'today' => $playsToday,
                'week' => $playsThisWeek,
                'month' => $playsThisMonth,
                'year' => $playsThisYear - 1,
            ],
        ]);
    }

    public function recordUserPlay($mediaId = false)
    {
        $userId = Auth::user()->id;

        if (! $userId || ! $mediaId) {
            return response()->json([
                'code' => 400,
                'message' => 'Bad params given',
            ]);
        }

        $created = UserMediaPlays::create([
            'user_id' => $userId,
            'media_id' => $mediaId,
        ]);

        if ($created) {
            return response()->json([
                'code' => 200,
                'message' => 'success',
            ]);
        } else {
            return response()->json([
                'code' => 400,
                'message' => 'could not log play',
            ]);
        }
    }
}
