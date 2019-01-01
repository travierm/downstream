<?php

namespace App\Http\Controllers\API;

use Auth;
use App\UserSpotifyToken;
use App\Services\SpotifyAPI;
use Illuminate\Http\Request;
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
}
