<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DB;
use Auth;
use App\User;
use App\UserMedia;

use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        $followingUserIds = Auth::user()->following()->pluck('follow_id');

        $date = \Carbon\Carbon::today()->subDays(14);
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

        return view('user.feed', [
            'items' => $items
        ]);
    }
}
