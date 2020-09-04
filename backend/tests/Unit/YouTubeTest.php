<?php

namespace Tests\Feature;

use App\Media\YouTube;
use YouTubeService;

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
        $query = "kid cudi";

        $resp = YouTubeService::search($query, $limit);
        
        $vid = $resp[0]->id->videoId;
        
        if($vid) {
            $test = true;
        }else{
            $test = false;
        }
        
        $this->assertTrue($test);
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
