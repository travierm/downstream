<?php

namespace App\Http\Controllers\API;

use App\Services\SpotifyAPI;
use Illuminate\Http\Request;
use App\Models\UserSpotifyToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SpotifyController extends Controller
{
    public function getAuthorizeUrl()
    {
        return SpotifyAPI::getAuthorizeUrl();
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

        return response()->json([], 200);
    }

    public function disableAccess()
    {
        $userId = Auth::user()->id;

        if (!$userId) {
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
