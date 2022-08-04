<?php

namespace App\Http\Controllers\API;

use App\Services\SpotifyAPI;
use Illuminate\Http\Request;
use App\Models\UserSpotifyToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUserInfo(Request $req)
    {
        return $req->user();
    }
}
