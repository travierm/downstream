<?php

namespace Tests\Feature;

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
        $query = "drake";

        $resp = YouTubeService::search($query, $limit);

        $vid = $resp[0]->id->videoId;
        
        if($vid) {
            $test = true;
        }else{
            $test = false;
        }
        
        $this->assertTrue($test);
    }
}
