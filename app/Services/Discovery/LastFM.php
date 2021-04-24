<?php
namespace App\Services\Discovery;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class LastFM {
  public static function trackSearch(string $track)
  {
    $response = Http::get('http://ws.audioscrobbler.com/2.0', [
      'format' => 'json',
      'method' => 'track.search',
      'track' => $track,
      'limit' => 4,
      'api_key' => Config::get('app.last_fm_api')
    ]);

    if($response->successful()) {
      dd($response->json());
    }
  }

  public static function similarTracks(string $track)
  {
    $response = Http::get('http://ws.audioscrobbler.com/2.0', [
      'format' => 'json',
      'method' => 'track.getsimilar',
      //'artist' => $artist,
      'track' => $track,
      'limit' => 50,
      'api_key' => Config::get('app.last_fm_api')
    ]);

    $tracks = [];
    if($response->successful()) {

      dd($response->json());
      $tracks = self::similarTracksReduce($response->json());
    }

    return $tracks;
  }

  public static function similarTracksReduce($json)
  {
    $tracks = $json['similartracks']['track'];

    return array_reduce($tracks, function($accumlator, $track) {
      $accumlator[] = [
        'track_name' => $track['name'],
        'track_artist' => $track['artist']['name']
      ];

      return $accumlator;
    }, []);
  }
}

?>



