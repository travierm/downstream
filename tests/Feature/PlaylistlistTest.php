<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

global $user;

class PlaylistTest extends TestCase
{
    public function setUp(): void
    {
        global $user;

        parent::setUp();

        if (!$user) {
            $user = User::factory()->make();
        }
    }

    // Create list
    public function testCanCreateList()
    {
        global $user;

        $response = $this->actingAs($user)->post('/api/playlist/create', [
          'name' => 'Big Chillin'
        ]);

        $response->assertStatus(200);
    }

    // Get all lists
    public function testCanGetAllLists() {
      global $user;

      $response = $this->actingAs($user)->get('/api/playlist/all');
      $response->assertStatus(200);
    }

    // Delete list
    public function testCanDeleteList() {
      global $user;

      $response = $this->actingAs($user)->delete('/api/playlist/delete/1');
      $response->assertStatus(200);
    }

    // Add item to list
    public function testCanAddItemToList() {
      global $user;

      $response = $this->actingAs($user)->get('/api/playlist/1/add', [
        'media_id' => 1
      ]);

      $response->assertStatus(200);
    }

    // See items in list
    public function testCanSeeItemInList() {
      global $user;

      $response = $this->actingAs($user)->get('/api/playlist/1');
      $response->assertStatus(200);
    }

    // Dekete item from list
    public function testCanDeleteItemFromList() {
      global $user;

      $response = $this->actingAs($user)->delete('/api/playlist/1/delete', [
        'media_id' => 1
      ]);

      $response->assertStatus(200);
    }
}

?>