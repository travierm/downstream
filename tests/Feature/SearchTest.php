<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanGetSearchResults()
    {
        $user = User::factory()->make();
        $response = $this->actingAs($user)->get('/api/search/drake');
        $response->assertStatus(200);

        $jsonData = $response->decodeResponseJson();

        $this->assertGreaterThanOrEqual(3, count($jsonData['results']), "Results returned 3 or more items");        
    }
}
