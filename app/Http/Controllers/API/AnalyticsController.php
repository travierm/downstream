<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Models\UserMediaPlays;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function recordUserPlay($mediaId = false)
    {
        $userId = Auth::user()->id;

        if(!$userId || !$mediaId) {
            return response()->json([
                'code' => 400,
                'message' => "Bad params given"
            ]);
        }

        $created = UserMediaPlays::create([
            'user_id' => $userId,
            'media_id' => $mediaId
        ]);

        if($created) {
            return response()->json([
                'code' => 200,
                'message' => "success"
            ]);
        }else{
            return response()->json([
                'code' => 400,
                'message' => "could not log play"
            ]);
        }
    }
}
