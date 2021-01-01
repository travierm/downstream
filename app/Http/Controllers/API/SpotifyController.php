<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Models\UserSpotifyToken;
use App\Services\SpotifyAPI;
use App\Http\Controllers\Controller;

class SpotifyController extends Controller
{
    public $scopes = [
        'user-top-read',
        'user-read-private',
        'playlist-read-private',
        'playlist-modify-private',
        'playlist-modify-public'
    ];

    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function getAuthorizeUrl() {
        return SpotifyAPI::getAuthorizeUrl();
    }

    public function disableAccess() {
        $userId = Auth::user()->id;

        if(!$userId) {
            return response()->json([
                'code' => 400,
                'message' => "Bad user_id"
            ]);
        }

        UserSpotifyToken::where("user_id", $userId)->delete();

        return response()->json([
            'code' => 200,
            'message' => "Successfully disabled access to Spotify"
        ]);
    }
}
