<?php

namespace Tests\Unit;

use App\Models\Media;
use App\Services\Discovery\SimilarTracks;
use Tests\TestCase;

class SimilarTracksTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanFindSimilarTracks()
    {
        $this->markTestSkipped('needs work');

        // Kid Cudi
        $media = Media::where('index', 'ppSY98RGyBU')->first();
        $tracks = SimilarTracks::similarTracksByMedia($media);

        $this->assertTrue(count($tracks) >= 1, 'can find similar tracks');
    }
}
