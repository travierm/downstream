<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserInviteCode;
use App\Models\UserWaitList;
use Tests\TestCase;

global $user;

class UserRegistrationControllerTest extends TestCase
{
    public function setUp(): void
    {
        global $user;

        parent::setUp();

        if (! $user) {
            $user = User::factory()->make();
        }
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanJoinWaitList()
    {
        global $user;

        // Create a sign up
        $response = $this->post('/api/waitlist/signup', [
            'email' => 'test@gmail.com',
            'textResponse' => 'i like kid cudi',
        ])->assertStatus(200);

        $jsonData = $response->decodeResponseJson();

        $signup = UserWaitList::first();

        $this->assertEquals($signup->email, 'test@gmail.com');
        $this->assertEquals($signup->text_response, 'i like kid cudi');
        $this->assertEquals('You have successfully joined the waiting list!', $jsonData['message'], 'The proper response message is returned');

        // Make sure we can't duplicate sign ups and get proper response
        $response = $this->post('/api/waitlist/signup', [
            'email' => 'test@gmail.com',
            'textResponse' => 'i like kid cudi2',
        ])->assertStatus(400);

        $jsonData = $response->decodeResponseJson();

        $count = UserWaitList::count();

        $this->assertEquals(1, $count, 'only one sign up exists on the wait list');
        $this->assertEquals('You have already joined the waiting list', $jsonData['message'], 'The proper response message is returned');
    }

    public function testCanRegisterWithInviteCode()
    {
        global $user;
        $testEmail = 'test99@gmail.com';

        // Create a sign up
        $response = $this->actingAs($user)->post('/api/waitlist/signup', [
            'email' => $testEmail,
            'textResponse' => 'i like drake',
        ])->assertStatus(200);

        $jsonData = $response->decodeResponseJson();
        $signup = UserWaitList::orderBy('id', 'DESC')->first();

        $invite = UserInviteCode::createInvite();
        $inviteCode = $invite->invite_code;

        $this->assertEquals($signup->email, $testEmail);
        $this->assertEquals('You have successfully joined the waiting list!', $jsonData['message'], 'The proper response message is returned');

        $this->post('/api/user/register', [
            'email' => $testEmail,
            'display_name' => 'test_account_123',
            'password' => 'test12345',
            'invite_code' => $inviteCode,
        ])->assertStatus(200);

        $userAccount = User::where('email', $testEmail)->orderBy('created_at', 'DESC')->first();
        $waitListRow = UserWaitList::where('email', $testEmail)->first();

        $this->assertEquals($testEmail, $userAccount->email);
        $this->assertEquals('test_account_123', $userAccount->display_name);
        $this->assertEquals(1, $waitListRow->created_account);
        $this->assertFalse(UserInviteCode::codeIsValid($inviteCode));

        $this->post('/api/user/register', [
            'email' => $testEmail,
            'display_name' => 'test_account_123',
            'password' => 'test123',
            'invite_code' => 'deff_not_an_invite_code',
        ])->assertStatus(400);
    }
}
