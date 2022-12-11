<?php

namespace Tests\Unit;

use App\Services\YoutubeService;
use Tests\TestCase;

class YoutubeServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanSearchForVideos()
    {
        $query = 'Lil Peep - cobain';
        $videos = YoutubeService::searchByQuery($query);

        $this->assertTrue(is_string($videos[0]->title), 'Search result has title');
        $this->assertTrue(is_string($videos[0]->videoId), 'Search result has videoId');
        $this->assertTrue(is_string($videos[0]->thumbnail), 'Search result has thumbnail');
    }

    public function testCanGetVideoById()
    {
        //Drake - In my feelings
        $video = YoutubeService::getVideoById('DRS_PpOrUZ4');

        //video should have all info filled or the api has most likely changed
        $this->assertTrue(is_string($video->title), 'Search result has title');
        $this->assertTrue(is_string($video->videoId), 'Search result has videoId');
        $this->assertTrue(is_string($video->thumbnail), 'Search result has thumbnail');
    }
}
