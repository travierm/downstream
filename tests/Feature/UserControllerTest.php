<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Media;
use App\Models\UserMedia;
use Illuminate\Support\Collection;

class UserControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_get_active_users_list()
    {
        $authedUser = User::factory()->create();
        $users = User::factory()->times(5)->create();

        $firstUser = $users->splice(0, 1)->first();
        UserMedia::factory()->times(5)->create([
            'user_id' => $firstUser->id
        ]);

        $response = $this->actingAs($authedUser)
            ->getJson('/api/users/active');

        $json = $response->decodeResponseJson();

        $this->assertCount(5, $json);
        $this->assertEquals($firstUser->id, $json[0]['id'], "first user in list has most items collected");
    }
}
