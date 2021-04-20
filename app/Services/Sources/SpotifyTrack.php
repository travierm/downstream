<?php
namespace App\Services\Sources;

use App\Services\SpotifyAPI;

class SpotifyTrack {
  public static function findByTitle($title)
  {
    $api = SpotifyAPI::getInstance();

    if(!$api) {
      return false;
    }

    try {
      $response = $api->search($title, 'track', [
        'limit' => 1
      ]);
    } catch(\Exception $e) {
      throw $e;
    }

    $responseItems = @$response->tracks->items;
    
    return count($responseItems) >= 1 ? $responseItems : false;
  }
}
