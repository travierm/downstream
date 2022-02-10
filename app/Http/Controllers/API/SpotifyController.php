<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserSpotifyToken;
use App\Services\SpotifyAPI;
use Auth;

class SpotifyController extends Controller
{
    public $scopes = [
        'user-top-read',
        'user-read-private',
        'playlist-read-private',
        'playlist-modify-private',
        'playlist-modify-public',
    ];

    public function getAuthorizeUrl()
    {
        $url = SpotifyAPI::getAuthorizeUrl();

        if (!$url) {
            return response()->json([
                'code' => 500,
                'message' => "Could not fetch Authorization URL",
            ]);
        }

        return response()->json([
            'code' => 200,
            'url' => $url,
        ]);
    }

    public function disableAccess()
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json([
                'code' => 400,
                'message' => "Bad user_id",
            ]);
        }

        UserSpotifyToken::where("user_id", $userId)->delete();

        return response()->json([
            'code' => 200,
            'message' => "Successfully disabled access to Spotify",
        ]);
    }
}
