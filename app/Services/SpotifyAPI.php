<?php
namespace App\Services;

use SpotifyWebAPI;

class SpotifyAPI {
	private static $api = false;
	private static $session = false;

	private static function boot() 
	{
		$clientId = env('SPOTIFY_CLIENT_ID');
		$clientSecret = env('SPOTIFY_CLIENT_SECRET');

		if(!$clientId or !$clientSecret) {
			return false;
		}


		$session = new SpotifyWebAPI\Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET')
        );

        $session->requestCredentialsToken();
        $accessToken = $session->getAccessToken();

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        self::$api = $api;
        self::$session = $session;

        return true;
	}

	public static function getInstance() 
	{
		if(!self::$api) {
			self::boot();
		}

		return self::$api;
	}
}