<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_get_active_users_list()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)
            ->getJson('/api/users/active');
        
        dd($response->getContent());
    }
}