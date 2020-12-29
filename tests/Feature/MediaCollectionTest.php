<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaCollectionTest extends TestCase
{
    private $collectedItemId;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->make();
    }

    public function testCanCollectAndRemoveItemFromCollection()
    {
        // Collect
        $response = $this->actingAs($this->user)->post('/api/media/collect', [
            // Kid Cudi - Tequila Shots
            'videoId' => 'lZcRSy0sk5w'
        ]);

        $mediaId = $response->json()['mediaId'];
        $this->assertNotEmpty($mediaId, 'Got mediaId back from route');
        $response->assertStatus(200);
        
        // Remove
        $response = $this->actingAs($this->user)->delete('/api/media/collection/' . $mediaId);
        $response->assertStatus(200);
    }
}
