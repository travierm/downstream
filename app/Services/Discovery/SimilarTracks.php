<?php
namespace App\Services\Discovery;

class SimilarTracks {
  public static function similarByTrackName(string $trackName)
  {
    $similarTracks = [
      'track_name' => 'Low Life',
      'track_artist' => 'Future'
    ];
    // search Last FM API for track names plus arts
    // search Spotify API for tracks
    // run similar tracks through youtube matcher
    // return youtube media type
    // look at search controller for reference
  }
}