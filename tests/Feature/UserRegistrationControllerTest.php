<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserRegistrationControllerTest extends TestCase
{
    private array $verificationResult = ['success' => true, 'action' => 'register', 'hostname' => 'localhost'];

    private int $verificationStatus = 200;

    private static int $client = 0;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.turnstile.secret_key' => 'test-secret']);
        config(['services.turnstile.hostnames' => ['localhost', '127.0.0.1']]);
        Http::preventStrayRequests();
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.'.++self::$client]);
        Http::fake(fn () => Http::response($this->verificationResult, $this->verificationStatus));
    }

    private function registration(array $overrides = []): array
    {
        return array_merge([
            'email' => 'new@example.com',
            'display_name' => 'New listener',
            'password' => 'test12345',
            'password_confirmation' => 'test12345',
            'cf-turnstile-response' => 'test-token',
        ], $overrides);
    }

    public function test_anyone_can_register_with_verified_turnstile_without_an_invite(): void
    {
        $this->postJson('/api/user/register', $this->registration())->assertOk();

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertEquals('New listener', $user->display_name);
        $this->assertTrue(Hash::check('test12345', $user->password));
        Http::assertSent(fn ($request) => $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $request['secret'] === 'test-secret'
            && $request['response'] === 'test-token'
        );
    }

    public function test_missing_token_cannot_create_an_account(): void
    {
        $data = $this->registration();
        unset($data['cf-turnstile-response']);
        $this->postJson('/api/user/register', $data)
            ->assertUnprocessable()->assertJsonValidationErrors('cf-turnstile-response');
        $this->assertDatabaseCount('users', 0);
        Http::assertNothingSent();
    }

    public function test_invalid_expired_or_replayed_tokens_cannot_create_an_account(): void
    {
        $this->verificationResult = ['success' => false, 'error-codes' => ['timeout-or-duplicate']];
        $this->postJson('/api/user/register', $this->registration())
            ->assertUnprocessable()->assertJsonValidationErrors('cf-turnstile-response');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_token_for_another_action_is_rejected(): void
    {
        $this->verificationResult = ['success' => true, 'action' => 'login'];
        $this->postJson('/api/user/register', $this->registration())->assertUnprocessable();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_verification_fails_closed_without_a_secret(): void
    {
        config(['services.turnstile.secret_key' => null]);
        $this->postJson('/api/user/register', $this->registration())->assertStatus(503);
        $this->assertDatabaseCount('users', 0);
        Http::assertNothingSent();
    }

    public function test_token_from_an_unapproved_hostname_is_rejected(): void
    {
        $this->verificationResult['hostname'] = 'other.example.com';
        $this->postJson('/api/user/register', $this->registration())
            ->assertUnprocessable()->assertJsonValidationErrors('cf-turnstile-response');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_token_without_a_hostname_is_rejected(): void
    {
        unset($this->verificationResult['hostname']);
        $this->postJson('/api/user/register', $this->registration())->assertUnprocessable();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_is_unavailable_without_expected_hostnames(): void
    {
        config(['services.turnstile.hostnames' => []]);
        $this->postJson('/api/user/register', $this->registration())->assertStatus(503);
        $this->assertDatabaseCount('users', 0);
        Http::assertNothingSent();
    }

    public function test_production_rejects_localhost_even_when_misconfigured(): void
    {
        $this->app->instance('env', 'production');
        config(['services.turnstile.hostnames' => ['downstream.us', 'localhost']]);
        $this->postJson('/api/user/register', $this->registration())->assertUnprocessable();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_production_accepts_the_approved_frontend_hostname(): void
    {
        $this->app->instance('env', 'production');
        config(['services.turnstile.hostnames' => ['downstream.us']]);
        $this->verificationResult['hostname'] = 'downstream.us';
        $this->postJson('/api/user/register', $this->registration())->assertOk();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_verification_fails_closed_on_connection_failure(): void
    {
        Http::fake(fn () => throw new ConnectionException('Timed out'));
        $this->postJson('/api/user/register', $this->registration())->assertStatus(503);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_verification_fails_closed_on_cloudflare_error(): void
    {
        $this->verificationStatus = 500;
        $this->postJson('/api/user/register', $this->registration())->assertStatus(503);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_invalid_registration_fields_are_rejected_before_verification(): void
    {
        $this->postJson('/api/user/register', $this->registration([
            'email' => 'invalid',
            'display_name' => ['invalid'],
            'password_confirmation' => 'does-not-match',
            'cf-turnstile-response' => str_repeat('a', 2049),
        ]))->assertUnprocessable()->assertJsonValidationErrors([
            'email', 'display_name', 'password', 'cf-turnstile-response',
        ]);
        $this->assertDatabaseCount('users', 0);
        Http::assertNothingSent();
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'new@example.com']);
        $this->postJson('/api/user/register', $this->registration())
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertDatabaseCount('users', 1);
        Http::assertNothingSent();
    }

    public function test_waitlist_signup_is_removed(): void
    {
        $this->postJson('/api/waitlist/signup', ['email' => 'new@example.com'])->assertNotFound();
    }

    public function test_registration_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/user/register', [])->assertUnprocessable();
        }
        $this->postJson('/api/user/register', [])->assertStatus(429);
    }
}
