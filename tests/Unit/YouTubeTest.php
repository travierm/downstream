<?php

namespace Tests\Feature;

use YouTubeService;
use App\Media\YouTube;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class YouTubeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSearch()
    {   
        $limit = 1;
        $query = "Lil Peep - cobain";

        $response = YouTubeService::search($query, $limit);
        
        $vid = $response[0]->id->videoId;
        
        $this->assertTrue(is_string($vid));
    }

    public function testGetVideoInfo()
    {
        $api = new YouTube();

        //Drake - In my feelings
        $video = $api->getById("DRS_PpOrUZ4");

        //video should have all info filled or the api has most likely changed
        $this->assertTrue($video->allKeysFilled());
    }
}
