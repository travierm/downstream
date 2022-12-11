<?php

namespace Tests\Unit;

use App\Services\Sources\SpotifyTrack;
use Tests\TestCase;

class SpotifyTrackTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanFindTrackByTitle()
    {
        $this->markTestSkipped('needs work');
        $tracks = SpotifyTrack::findByTitle('Lil Peep - ghost girl');

        $this->assertTrue(! empty($tracks[0]->id), 'Can get Spotify Track ID');
    }

    public function testCanGetSeedTracks()
    {
        $this->markTestSkipped('needs work');

        // Lil Peep - Ghost Girl
        $spotifyTrackId = '5LAVhynnTmj9VJjW8xGZdP';

        $results = SpotifyTrack::getSeedTracksByIds([$spotifyTrackId]);
        $this->assertTrue(count($results) >= 2, 'Got more then 2 tracks back from seed track');

        $results = SpotifyTrack::getSeedTracksByIds(['sdfs']);
        $this->assertFalse($results, 'Got no tracks back using bad seed track id');
    }
}
