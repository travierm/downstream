<?php
namespace App\Services\Sources;

use Log;
use App\Services\SpotifyAPI;

class SpotifyTrack {
  public static function findIdByTitle(String $title)
  {
    $tracks = self::findByTitle($title);

    
    if(@$tracks[0]) {
      return $tracks[0]->id;
    }

    return false;
  }

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

  public static function getSeedTracksByIds(Array $ids = [], $limit = 8) {
    $api = SpotifyAPI::getInstance();

    $results = false;
    try {
      $results = $api->getRecommendations([
        'limit' => $limit,
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
