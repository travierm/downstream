<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\UserData;

global $user;

class DiscoverTrackTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        global $user;

        parent::setUp();
        $this->setUpFaker();

        if (! $user) {
            $user = User::factory()->make();
        }

        UserData::setUser($user->id);
    }

    public function testCanGetBadVideoIdError()
    {
        global $user;

        $response = $this->actingAs($user)->get('/api/discover/track/asd');
        $response->assertStatus(500);
    }

    public function testCanGetSimilarTrackResults()
    {
        global $user;

        $this->markTestSkipped('needs work');

        // Kodak Black - Calling my Spirit
        $videoId = 'ppSY98RGyBU';

        $response = $this->actingAs($user)->get("/api/discover/track/{$videoId}");
        $response->assertStatus(200);

        $results = $response->json();

        $this->assertTrue(array_key_exists('items', $results), 'response has items key');
        $this->assertTrue(count($results['items']) >= 1, 'One or more items returns');
    }
}
