<?php

namespace App\Http\Controllers\API;

use Auth;
use App\GlobalQueue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GlobalController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function postQueue(Request $request)
    {
        $userId = Auth::user()->id;
        $mediaId = $request->input('mediaId');

        if($mediaId == NULL || $userId == NULL) {
            return response()->json([
                'code'      =>  401,
                'message'   =>  "Bad auth or mediaId"
              ], 401);
        }

        $item = new GlobalQueue;
        $item->user_id = $userId;
        $item->media_id = $mediaId;

        if(!GlobalQueue::canPushItem($item)) {
            return response()->json([
                'code' => 401,
                'message' => "item already on queue"
            ]);
        }else{
            $item->save();
        }

        return response()->json([
            'code'      =>  200,
            'message'   =>  "Pushed to global queue"
          ], 200);
    }
}
