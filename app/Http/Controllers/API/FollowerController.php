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
        $authUser = Auth::user();
        $followId = $request->followId;

        if ($authUser->id == $followId) {
            return response()->json([
                'message' => "Cannot follow yourself"
            ], 400);
        }

        if($authUser->isFollowing($followId)) {
            return response()->json([
                'message' => "Already following user"
            ], 400);
        }

        $followUser = User::find($followId);
        if(!$followUser) {
            return response()->json([
                'message' => "Can not follow unknown user"
            ], 400);
        }

        $authUser->following()->save($followUser);

        $followedUser = new \stdClass();
        $followedUser->id = $followUser->id;
        $followedUser->hash = $followUser->hash;
        $followedUser->display_name = $followUser->display_name;
        $followedUser->guid = "guid_" . Str::random(35);

        return response()->json([
            'message' => "You are now following the user",
            'followedUser' => $followedUser
        ], 200);
    }

    public function unfollow(Request $request)
    {
        $authUser = Auth::user();
        $followId = $request->followId;

        if(!$authUser->isFollowing($followId)) {
            return response()->json([
                'message' => "You are not following this user"
            ], 400);
        }

        DB::table('followers')
            ->where('follow_id', $followId)
            ->where('user_id', $authUser->id)
            ->delete();

        return response()->json([
            'message' => "Unfollowed user"
        ], 200);
    }

    public function getFollowage()
    {
        $authUser = Auth::user();

        $user_followers = $authUser->followers()->get();
        $followers = [];
        foreach($user_followers as $follower) {
            $result = new \stdClass();
            $result->id = $follower->id;
            $result->hash = $follower->hash;
            $result->display_name = $follower->display_name;
            $result->guid = "guid_" . Str::random(35);

            $followers[] = $result;
        }

        $user_following = $authUser->following()->get();
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
