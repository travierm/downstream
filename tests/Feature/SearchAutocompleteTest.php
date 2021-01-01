<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchAutocompleteTest extends TestCase
{

    /**
     * Make sure the controller can fetch autocomplete data from the external API
     *
     * @return void
     */
    public function testCanFetchAutocompleteResults()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)
            ->get('/api/search/autocomplete/drake');

        $response->assertStatus(200)->assertJson([
            'results' => true,
        ]);
    }
}
