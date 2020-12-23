<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $user = factory('App\User')->make();

        $response = $this->actingAs($user)
            ->get('/api/search/autocomplete/drake');

        $response->assertStatus(200)->assertJson([
            'results' => true,
        ]);
    }
}
