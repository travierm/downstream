<?php

namespace App\Services\Spotify;

use App\Models\UserSpotifyToken;
use SpotifyWebAPI\Session;

class UserSpotifySession extends Session
{
    public function __construct(private UserSpotifyToken $token)
    {
        parent::__construct(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_CONNECT_URL')
        );
        $this->setAccessToken($token->access_token);
        $this->setRefreshToken($token->refresh_token);
    }

    public function refreshAccessToken($refreshToken = null): bool
    {
        if (! parent::refreshAccessToken($refreshToken)) {
            return false;
        }

        // The SDK refreshes lazily during requests. Persist rotation before it retries.
        $this->token->access_token = $this->getAccessToken();
        $this->token->refresh_token = $this->getRefreshToken();
        $this->token->save();

        return true;
    }
}
