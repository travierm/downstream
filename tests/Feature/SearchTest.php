<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

global $user;

class SearchTest extends TestCase
{
    public function setUp(): void
    {
        global $user;

        parent::setUp();

        if (!$user) {
            $user = User::factory()->make();
        }
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanGetSearchResults()
    {
        global $user;
        
        $response = $this->actingAs($user)->get('/api/search/drake');
        $response->assertStatus(200);

        $jsonData = $response->decodeResponseJson();

        $this->assertGreaterThanOrEqual(3, count($jsonData['results']), "Results returned 3 or more items");
    }

    public function testCanSeeCollectedItemInSearchResults()
    {
        global $user;
        $testVideoId = 'lZcRSy0sk5w';
        // Collect
        $collectedMediaId = $this->actingAs($user)->post('/api/media/collect', [
            // Kid Cudi - Tequila Shots
            'videoId' => $testVideoId
        ])->assertStatus(200)->decodeResponseJson()['mediaId'];

        $response = $this->actingAs($user)->get('/api/search/lZcRSy0sk5w')->assertStatus(200);

        $jsonData = $response->decodeResponseJson();
        $results = $jsonData['results'];

        $this->assertTrue(count($results) >= 1, 'Got 1 or more results back from search');

        $foundMatch = false;
        foreach($results as $video) {
            if($video['videoId'] == $testVideoId) {
                
                $foundMatch = true;
                $this->assertTrue($video['collected'] == true, 'Collected videoId has property collected in search results');
                $this->assertTrue($video['mediaId'] == $collectedMediaId, 'Collected mediaId matches mediaId in search results');
            }
        }

        $this->assertTrue($foundMatch, 'Collected videoId was found in search results');
    }
}
