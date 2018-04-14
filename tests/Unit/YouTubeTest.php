<?php

namespace Tests\Feature;

use App\Media\YouTubeV2;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    	$limit = 1;

        $results = YouTubeV2::search($query, $limit);
        $test = (count($results) >= 1);
        
        $this->assertTrue($test);
    }
}
