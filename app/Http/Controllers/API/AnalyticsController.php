<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Models\UserMediaPlays;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function recordUserPlay(Request $request)
    {
        if(!Auth::user()->id) {
            return response()->json([
                'code' => 400,
                'message' => "Bad user_id"
            ]);
        }

        $created = UserMediaPlays::create([
            'user_id' => Auth::user()->id,
            'media_id' => $request->media_id
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
