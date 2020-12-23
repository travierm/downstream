<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchAutocompleteTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = factory('App\User')->make();

        $response = $this->actingAs($user)
            ->get('/api/search/autocomplete/drake');
            
        $response->assertStatus(200)->assertJson([
            'results' => true,
        ]);
    }
}
