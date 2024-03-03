<?php

namespace App\Http\Controllers;

use Cache;
use Carbon\Carbon;
use DB;
use Auth;
use App\Models\User;
use App\Models\UserMedia;

use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $followingUserIds = Auth::user()->following()->pluck('follow_id');

        $date = \Carbon\Carbon::today()->subDays(30);
        $followingMedia = DB::table('user_media')
            ->whereIn('user_id', $followingUserIds)
            ->where('created_at', '>=', $date)
            ->orderBy('created_at', 'DESC')
            ->get();

        $items = [];
        foreach($followingMedia as $userMedia) {
            $media = getMediaByIds([$userMedia->media_id])[0];

            $followingName = getUserDisplayName($userMedia->user_id);

            $media->following_name = $followingName;

            $date = Carbon::parse($userMedia->created_at);
            $now = Carbon::now();
            $diff = $date->diffInDays($now);

            $media->days_since = $diff;

            $items[] = $media;
        }

        //update activity feed last check
        Cache::forever(Auth::user()->id . "_activity_last_check", date("Y-m-d H:i:s"));

        return view('user.feed', [
            'items' => $items,
            'followingCount' => count($followingUserIds)
        ]);
    }
}
