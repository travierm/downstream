<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

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
