<?php
namespace App\Services\Sources;

use Log;
use App\Services\SpotifyAPI;

class SpotifyTrack {
  public static function findByTitle(String $title)
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

  public static function getSeedTracksByIds(Array $ids = []) {
    $api = SpotifyAPI::getInstance();

    $results = false;
    try {
      $results = $api->getRecommendations([
        'limit' => 8,
        'seed_tracks' => $ids
      ]);
    } catch(\Exception $e) {
      Log::error($e->getMessage());
    }

    if(!$results) {
      return false;
    }

    $tracks = $results->tracks;
    
    return count($tracks) >= 1 ? $tracks : false;
  }
}
