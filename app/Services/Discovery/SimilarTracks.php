<?php
namespace App\Services\Discovery;

use App\Models\Media;
use App\Services\YoutubeService;
use App\Services\Sources\SpotifyTrack;
use App\MediaType\Transformer\SpotifyTrackTransformer;
class SimilarTracks {
  public static function similarTracksByMedia(Media $media, $limit = 24)
  {
    $similarTracks = [];

    // Get Spotify ID of Track
    $spotifyId = $media->getOrFindSpotifyId();
  
    if(!$spotifyId) {
      throw new \Exception("Could not match video_id to discovery services");

      return false;
    }
    
    // Use given track as a seed to find similar tracks on Spotify
    $seedTracks = SpotifyTrack::getSeedTracksByIds([$spotifyId], $limit);
    foreach($seedTracks as $track) {

      // Convert Spotify results to array of data
      $trackData = SpotifyTrackTransformer::transform($track);

      // Search for YouTube Video by title of Spotify Track
      $youtubeSearchResults = YouTubeService::searchByQuery($trackData['title'], 1);
      if($youtubeSearchResults[0]) {
        // Add first search result to the array of similar tracks
        $similarTracks[] = $youtubeSearchResults[0];
      }
    }
    
    return $similarTracks;
  }
}