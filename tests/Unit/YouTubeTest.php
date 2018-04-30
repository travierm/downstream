<?php

namespace Tests\Feature;

use App\Media\YouTubeV2;
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
    	$query = "drake";

        $resp = YouTubeV2::searchFirst($query);

        if($resp->vid) {
            $test = true;
        }else{
            $test = false;
        }
        
        $this->assertTrue($test);
    }
}
