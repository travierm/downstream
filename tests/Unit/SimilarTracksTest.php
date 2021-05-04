<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Media;
use App\Services\Discovery\SimilarTracks;

class SimilarTracksTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanFindSimilarTracks()
    {
      // Kid Cudi
      $media = Media::where('index', 'ppSY98RGyBU')->first();
      $tracks = SimilarTracks::similarTracksByMedia($media);

      $this->assertTrue(count($tracks) >= 1, "can find similar tracks");
    }
}
