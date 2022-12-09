<?php

namespace App\Services;

use SpotifyWebAPI\Session;
use App\Models\UserSpotifyToken;
use Exception;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyAPI
{
    private static $booted = false;
    private static Session $session;
    private static ?SpotifyWebAPI $api = null;

    private static $scopes = [
        'user-top-read',
        'user-read-private',
        'playlist-read-private',
        'playlist-modify-private',
        'playlist-modify-public',
    ];

    public static function getAuthorizeUrl(): string
    {
        $session = self::getSession();
        if (!$session) {
            return false;
        }

        $state = $session->generateState();

        $options = [
            'scope' => self::$scopes,
            'state' => $state
        ];

        return $session->getAuthorizeUrl($options);
    }

    public static function getSession(): Session
    {
        if (!self::$booted) {
            self::boot();
        }

        return self::$session;
    }

    public static function getInstanceWithToken(UserSpotifyToken $token): SpotifyWebAPI
    {
        $session = new Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_CONNECT_URL')
        );

        $newAccessToken = $session->getAccessToken();
        $newRefreshToken = $session->getRefreshToken();

        if ($token) {
            $session->setAccessToken($token->access_token);
            $session->setRefreshToken($token->refresh_token);
        }

        $options = [
            'auto_refresh' => true,
        ];

        $api = new SpotifyWebAPI($options, $session);

        // check for refresh
        $newAccessToken = $session->getAccessToken();
        $newRefreshToken = $session->getRefreshToken();

        if ($token->access_token !== $newAccessToken) {
            echo "updating token";
            $token->access_token = $newAccessToken;
            $token->refresh_token = $newRefreshToken;
            $token->save();
        }

        return $api;
    }

    public static function getInstance(): SpotifyWebAPI
    {
        return self::$api ? self::$api : self::boot();
    }


    private static function boot(): SpotifyWebAPI
    {
        $clientId = env('SPOTIFY_CLIENT_ID');
        $clientSecret = env('SPOTIFY_CLIENT_SECRET');

        if (!$clientId or !$clientSecret) {
            throw \Exception('Missing Spotify secrets');
        }

        $session = new Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_CONNECT_URL')
        );

        $session->requestCredentialsToken();
        $accessToken = $session->getAccessToken();

        $api = new SpotifyWebAPI([], $session);
        $api->setAccessToken($accessToken);

        self::$api = $api;
        self::$session = $session;

        if ($api && $session) {
            self::$booted = true;
        }

        return self::$api;
    }
}
