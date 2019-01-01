<?php
namespace App\Services;

use SpotifyWebAPI;

class SpotifyAPI {

	private static $booted = false;
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
			env('SPOTIFY_CLIENT_SECRET'),
			'https://downstream.us/spotify/connect'
        );

        $session->requestCredentialsToken();
        $accessToken = $session->getAccessToken();

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        self::$api = $api;
		self::$session = $session;
		
		if($api && $session) {
			self::$booted = true;
		}

        return true;
	}

	public static function getSession() {
		if(!self::$booted) {
			self::boot();
		}

		return self::$session;
	}

	public static function getInstance() 
	{
		if(!self::$booted) {
			self::boot();
		}

		return self::$api;
	}
}