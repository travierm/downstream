<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Tests\UserData;
use Illuminate\Foundation\Testing\WithFaker;

global $user;

class DiscoverTrackTest extends TestCase
{
  use WithFaker;

  public function setUp(): void
  {
    global $user;

    parent::setUp();
    $this->setUpFaker();

    if (!$user) {
      $user = User::factory()->make();
    }

    UserData::setUser($user->id);
  }

  public function testCanGetBadVideoIdError()
  {
    global $user;
    
    $response = $this->actingAs($user)->get('/api/discover/track/sIfTD2i50s8');
    $response->assertStatus(500);
  }
}