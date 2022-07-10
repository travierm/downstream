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

    public function getConnect(Request $request)
    {
        $code = $request->input('code');

        $session = SpotifyAPI::getSession();

        $session->requestAccessToken($code);
        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();

        $userId = Auth::user()->id;

        $token = UserSpotifyToken::where('user_id', $userId);
        if ($token) {
            //delete existing tokens
            $token = UserSpotifyToken::where('user_id', $userId)->delete();
        }

        $token = new UserSpotifyToken();
        $token->access_token = $accessToken;
        $token->refresh_token = $refreshToken;
        $token->user_id = $userId;
        $token->save();

        if ($token) {
            $success = true;
        } else {
            $success = false;
        }

        return redirect('localhost:8000/spotify');
    }
}
