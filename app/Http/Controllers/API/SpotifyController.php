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
        'playlist-read-private',
        'user-read-private'
    ];

    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function getAuthorizeUrl() {
        $session = SpotifyAPI::getSession();

        $options = ['scope' => $this->scopes];

        return $session->getAuthorizeUrl($options);
    }
}
