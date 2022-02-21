<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

global $user;

class _template extends TestCase
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
}
