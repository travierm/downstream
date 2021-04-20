<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Sources\SpotifyTrack;
use App\Services\YoutubeService;

class SpotifyTrackTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanFindTrackByTitle()
    {
      $tracks = SpotifyTrack::findByTitle("Lil Peep - ghost girl");

      $this->assertTrue(!empty($tracks[0]->id), 'Can get Spotify Track ID');
    }
}
