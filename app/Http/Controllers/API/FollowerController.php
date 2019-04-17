<?php

namespace App\Http\Controllers\API;

use DB;
use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FollowerController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function follow($followId)
    {
        $user = Auth::user();
        //$followId = $request->input('follow_id');
        
        if($user->isFollowing($followId)) {
            return response()->json([
                'code' => 401,
                'message' => "Already following user"
            ], 401);
        }

        $followUser = User::find($followId);
        if(!$followUser) {
            return response()->json([
                'code' => 401,
                'message' => "Can not follow unknown user"
            ], 401);
        }

        $user->following()->save($followUser);

        return response()->json([
                'code' => 200,
                'message' => "You are now following the user"
        ], 200);
    }

    public function unfollow($followId)
    {
        $user = Auth::user();

        if($user->isFollowing($followId)) {

            DB::table('followers')
                ->where('follow_id', $followId)
                ->where('user_id', $user->id)
                ->delete();

            return response()->json([
                'code' => 401,
                'message' => "Unfollowed user"
            ], 200);
        }

         return response()->json([
                'code' => 401,
                'message' => "Media not found or not owned by this user"
            ], 401);
    }

    public function getFollowers()
    {
        return response()->json(Auth::user()->following());
    }
}
