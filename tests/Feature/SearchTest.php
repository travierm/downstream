<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

global $user;

class SearchTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * A basic feature test example.
     * @group youtube
     * @return void
     */
    public function testCanGetSearchResults()
    {
        $response = $this->actingAs($this->user)->get('/api/search/drake');
        $response->assertStatus(200);

        $jsonData = $response->decodeResponseJson();

        $this->assertGreaterThanOrEqual(3, count($jsonData['results']), 'Results returned 3 or more items');
    }

    /**
     * @group youtube
     *
     * @return void
     */
    public function testCanSeeCollectedItemInSearchResults()
    {
        $this->actingAs($this->user);

        $testVideoId = 'lZcRSy0sk5w';

        // Collect
        $response = $this->post('/api/media/collect', [
            // Kid Cudi - Tequila Shots
            'videoId' => $testVideoId,
        ]);

        $response->assertStatus(200);
        $collectedMediaId = $response->decodeResponseJson()['mediaId'];

        $response = $this->get('/api/search/lZcRSy0sk5w')->assertStatus(200);

        $jsonData = $response->decodeResponseJson();
        $results = $jsonData['results'];

        $this->assertTrue(count($results) >= 1, 'Got 1 or more results back from search');

        $foundMatch = false;
        foreach ($results as $video) {
            if ($video['videoId'] == $testVideoId) {
                $foundMatch = true;
                $this->assertTrue($video['collected'] == true, 'Collected videoId has property collected in search results');
                $this->assertTrue($video['mediaId'] == $collectedMediaId, 'Collected mediaId matches mediaId in search results');
            }
        }

        $this->assertTrue($foundMatch, 'Collected videoId was found in search results');
    }
}
