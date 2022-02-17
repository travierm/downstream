<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserWaitList;
use Tests\TestCase;

global $user;

class UserRegistrationControllerTest extends TestCase
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

        // Create a sign up
        $response = $this->actingAs($user)->post('/api/waitlist/signup', [
            'email' => 'test@gmail.com',
            'textResponse' => 'i like kid cudi',
        ])->assertStatus(200);

        $jsonData = $response->decodeResponseJson();

        $signup = UserWaitList::first();

        $this->assertEquals($signup->email, 'test@gmail.com');
        $this->assertEquals($signup->text_response, 'i like kid cudi');
        $this->assertEquals("You have successfully joined the waiting list!", $jsonData['message'], "The proper response message is returned");

        // Make sure we can't duplicate sign ups and get proper response
        $response = $this->actingAs($user)->post('/api/waitlist/signup', [
            'email' => 'test@gmail.com',
            'textResponse' => 'i like kid cudi2',
        ])->assertStatus(400);

        $jsonData = $response->decodeResponseJson();

        $count = UserWaitList::count();

        $this->assertEquals(1, $count, "only one sign up exists on the wait list");
        $this->assertEquals("You have already joined the waiting list", $jsonData['message'], "The proper response message is returned");
    }
}
