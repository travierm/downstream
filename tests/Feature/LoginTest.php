<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginTest extends TestCase
{
    #[DataProvider('devices')]
    public function test_login_issues_a_hashed_token_that_authenticates_the_correct_user(array $device, string $name): void
    {
        $user = User::factory()->create();
        User::factory()->create();

        $response = $this->postJson('/api/auth/login', array_merge([
            'email' => $user->email, 'password' => 'password',
        ], $device))->assertOk()->assertJsonPath('user.id', $user->id);

        $plainToken = $response->json('token');
        $this->assertIsString($plainToken);
        $token = PersonalAccessToken::findToken($plainToken);
        $this->assertNotNull($token);
        $this->assertTrue($token->tokenable->is($user));
        $this->assertSame($name, $token->name);
        $this->assertSame(hash('sha256', explode('|', $plainToken, 2)[1]), $token->token);
        $this->assertDatabaseCount('personal_access_tokens', 1);
        foreach (['password', 'remember_token', 'api_token'] as $secret) {
            $response->assertJsonMissingPath('user.'.$secret);
        }

        $this->app['auth']->forgetGuards();
        $profile = $this->withToken($plainToken)->getJson('/api/auth/user')
            ->assertOk()->assertJsonPath('id', $user->id);
        foreach (['password', 'remember_token', 'api_token'] as $secret) {
            $profile->assertJsonMissingPath($secret);
        }

        $token->delete();
        $this->app['auth']->forgetGuards();
        $this->withToken($plainToken)->getJson('/api/auth/user')->assertUnauthorized();
    }

    public static function devices(): array
    {
        return ['default device' => [[], 'webapp'], 'named device' => [['device_name' => 'phone'], 'phone']];
    }

    #[DataProvider('invalidCredentials')]
    public function test_invalid_credentials_do_not_issue_tokens(string $email, string $password): void
    {
        User::factory()->create(['email' => 'listener@example.test']);

        $this->postJson('/api/auth/login', compact('email', 'password'))
            ->assertUnprocessable()->assertJsonValidationErrors('email')->assertJsonMissingPath('token');
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public static function invalidCredentials(): array
    {
        return [
            'wrong password' => ['listener@example.test', 'incorrect'],
            'unknown account' => ['missing@example.test', 'password'],
        ];
    }

    #[DataProvider('invalidInput')]
    public function test_invalid_login_input_is_rejected(array $input, string $field): void
    {
        $this->postJson('/api/auth/login', $input)->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public static function invalidInput(): array
    {
        return [
            'missing email' => [['password' => 'password'], 'email'],
            'invalid email' => [['email' => 'invalid', 'password' => 'password'], 'email'],
            'missing password' => [['email' => 'listener@example.test'], 'password'],
            'non-string password' => [['email' => 'listener@example.test', 'password' => ['password']], 'password'],
            'non-string device' => [['email' => 'listener@example.test', 'password' => 'password', 'device_name' => ['phone']], 'device_name'],
        ];
    }

    #[DataProvider('unauthorizedTokens')]
    public function test_protected_routes_reject_missing_or_invalid_tokens(?string $token): void
    {
        if ($token !== null) {
            $this->withToken($token);
        }
        $this->getJson('/api/auth/user')->assertUnauthorized();
        $this->getJson('/api/collection')->assertUnauthorized();
        $this->postJson('/api/media/collect', ['videoId' => 'video'])->assertUnauthorized();
        $this->postJson('/api/media/push/1')->assertUnauthorized();
        $this->deleteJson('/api/media/collection/1')->assertUnauthorized();
        $this->assertDatabaseCount('user_media', 0);
    }

    public static function unauthorizedTokens(): array
    {
        return ['no token' => [null], 'invalid token' => ['999|invalid-token']];
    }
}
