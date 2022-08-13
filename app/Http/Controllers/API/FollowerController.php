<?php

namespace App\Http\Controllers\API;

use DB;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;

class FollowerController extends Controller
{
    public function follow(Request $request)
    {
        $user = Auth::user();
        $followId = $request->input('follow_id');

        if($user->isFollowing($followId)) {
            return response()->json([
                'code' => 400,
                'message' => "Already following user"
            ], 401);
        }

        $followUser = User::find($followId);
        if(!$followUser) {
            return response()->json([
                'code' => 400,
                'message' => "Can not follow unknown user"
            ], 401);
        }

        $user->following()->save($followUser);

        return response()->json([
                'code' => 200,
                'message' => "You are now following the user"
        ], 200);
    }

    public function unfollow(Request $request)
    {
        $user = Auth::user();
        $followId = $request->input('follow_id');

        if($user->isFollowing($followId)) {

            DB::table('followers')
                ->where('follow_id', $followId)
                ->where('user_id', $user->id)
                ->delete();

            return response()->json([
                'code' => 400,
                'message' => "Unfollowed user"
            ], 200);
        }

         return response()->json([
                'code' => 400,
                'message' => "Media not found or not owned by this user"
            ], 401);
    }

    public function getFollowage()
    {
        $viewUser = Auth::user();

        $user_followers = $viewUser->followers()->get();
        $followers = [];
        foreach($user_followers as $follower) {
            $result = new \stdClass();
            $result->id = $follower->id;
            $result->hash = $follower->hash;
            $result->display_name = $follower->display_name;
            $result->guid = "guid_" . Str::random(35);

            $followers[] = $result;
        }

        $user_following = $viewUser->followers()->get();
        $following = [];
        foreach($user_following as $follower) {
            $result = new \stdClass();
            $result->id = $follower->id;
            $result->hash = $follower->hash;
            $result->display_name = $follower->display_name;
            $result->guid = "guid_" . Str::random(35);

            $following[] = $result;
        }

        return response()->json([
         'followers' => $followers,
         'following' => $following,
        ]);
    }
}
