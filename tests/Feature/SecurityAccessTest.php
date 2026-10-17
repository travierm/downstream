<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserMedia;
use App\Models\UserSpotifyToken;
use App\Services\SpotifyAPI;
use Illuminate\Support\Facades\Cache;
use Mockery;
use ReflectionProperty;
use SpotifyWebAPI\Session;
use Tests\TestCase;

class SecurityAccessTest extends TestCase
{
    public function test_private_collection_is_hidden_from_other_users_but_visible_to_owner()
    {
        $owner = User::factory()->create(['private' => true]);
        $viewer = User::factory()->create();
        $item = UserMedia::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($viewer)->getJson('/api/collection/'.$owner->hash)->assertNotFound();
        $this->actingAs($viewer)->getJson('/api/collection/missing-profile')->assertNotFound();
        $this->actingAs($owner)->getJson('/api/collection/'.$owner->hash)
            ->assertOk()->assertJsonPath('items.0.media_id', $item->media_id);
    }

    public function test_public_collection_can_still_be_viewed()
    {
        $owner = User::factory()->create();
        $item = UserMedia::factory()->create(['user_id' => $owner->id]);

        $this->actingAs(User::factory()->create())->getJson('/api/collection/'.$owner->hash)
            ->assertOk()->assertJsonPath('items.0.media_id', $item->media_id);
    }

    public function test_private_accounts_cannot_be_followed_and_existing_follows_do_not_leak_activity()
    {
        $viewer = User::factory()->create();
        $privateUser = User::factory()->create(['private' => true]);
        $publicUser = User::factory()->create();
        $privateItem = UserMedia::factory()->create(['user_id' => $privateUser->id]);
        $publicItem = UserMedia::factory()->create(['user_id' => $publicUser->id]);

        $this->actingAs($viewer)->putJson('/api/follow/'.$privateUser->id)->assertStatus(400);
        $this->assertDatabaseMissing('followers', ['user_id' => $viewer->id, 'follow_id' => $privateUser->id]);
        $this->actingAs($viewer)->putJson('/api/follow/'.$publicUser->id)->assertOk();
        $viewer->following()->attach($privateUser->id);

        $this->getJson('/api/following/activity')->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.id', $publicItem->media_id)
            ->assertJsonMissing(['id' => $privateItem->media_id]);
    }

    public function test_active_users_only_exposes_public_profile_fields()
    {
        $publicUser = User::factory()->create();
        $privateUser = User::factory()->create(['private' => true]);

        $response = $this->actingAs($publicUser)->getJson('/api/users/active')->assertOk();
        $this->assertCount(1, $response->json());
        $this->assertEqualsCanonicalizing(
            ['id', 'display_name', 'hash', 'media_count'],
            array_keys($response->json('0'))
        );
        $response->assertJsonMissing(['id' => $privateUser->id]);
        $this->getJson('/api/auth/user')->assertOk()->assertJsonPath('email', $publicUser->email);
    }

    public function test_spotify_callback_requires_state_from_the_same_user_and_preserves_existing_tokens()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->storeSpotifyToken($otherUser);
        $state = str_repeat('a', 64);
        Cache::put('spotify_oauth_state:'.$owner->id, $state, 600);

        $this->actingAs($otherUser)->postJson('/api/spotify/connect', ['code' => 'code'])
            ->assertUnprocessable()->assertJsonValidationErrors('state');
        $this->postJson('/api/spotify/connect', ['code' => 'code', 'state' => $state])
            ->assertUnprocessable()->assertJsonValidationErrors('state');
        $this->postJson('/api/spotify/connect', ['code' => 'code', 'state' => ['invalid']])
            ->assertUnprocessable()->assertJsonValidationErrors('state');
        $this->assertDatabaseHas('user_spotify_tokens', ['user_id' => $otherUser->id, 'access_token' => 'original']);
    }

    public function test_spotify_callback_rejects_mismatched_and_expired_states()
    {
        $user = User::factory()->create();
        $this->storeSpotifyToken($user);
        $this->actingAs($user);
        Cache::put('spotify_oauth_state:'.$user->id, str_repeat('a', 64), 600);
        $this->postJson('/api/spotify/connect', ['code' => 'code', 'state' => str_repeat('b', 64)])
            ->assertUnprocessable()->assertJsonValidationErrors('state');

        Cache::put('spotify_oauth_state:'.$user->id, str_repeat('a', 64), now()->addMinutes(10));
        $this->travel(11)->minutes();
        $this->postJson('/api/spotify/connect', ['code' => 'code', 'state' => str_repeat('a', 64)])
            ->assertUnprocessable()->assertJsonValidationErrors('state');
        $this->assertDatabaseHas('user_spotify_tokens', ['user_id' => $user->id, 'access_token' => 'original']);
    }

    public function test_spotify_state_is_issued_in_url_and_can_only_link_once()
    {
        $user = User::factory()->create();
        $this->storeSpotifyToken($user);
        $session = Mockery::mock(Session::class)->makePartial();
        $session->__construct('test-client', 'test-secret', 'https://example.test/spotify/connect');
        $session->shouldReceive('requestAccessToken')->once()->with('valid-code')->andReturn(true);
        $session->setAccessToken('new-access');
        $session->setRefreshToken('new-refresh');

        // The existing Spotify wrapper keeps its SDK session in static properties.
        $booted = new ReflectionProperty(SpotifyAPI::class, 'booted');
        $sessionProperty = new ReflectionProperty(SpotifyAPI::class, 'session');
        $previousBooted = $booted->getValue();
        $previousSession = $sessionProperty->isInitialized() ? $sessionProperty->getValue() : null;
        $booted->setValue(null, true);
        $sessionProperty->setValue(null, $session);

        try {
            $response = $this->actingAs($user)->get('/api/spotify/authorize')->assertOk();
            parse_str(parse_url($response->getContent(), PHP_URL_QUERY), $query);
            $this->assertSame(64, strlen($query['state']));
            $this->assertSame($query['state'], Cache::get('spotify_oauth_state:'.$user->id));
            $callback = ['code' => 'valid-code', 'state' => $query['state']];

            $this->postJson('/api/spotify/connect', $callback)->assertOk();
            $this->assertDatabaseHas('user_spotify_tokens', ['user_id' => $user->id, 'access_token' => 'new-access', 'refresh_token' => 'new-refresh']);
            $this->assertDatabaseCount('user_spotify_tokens', 1);
            $this->postJson('/api/spotify/connect', $callback)->assertUnprocessable()->assertJsonValidationErrors('state');
        } finally {
            $booted->setValue(null, $previousBooted);
            if ($previousSession !== null) {
                $sessionProperty->setValue(null, $previousSession);
            }
        }
    }

    private function storeSpotifyToken(User $user): void
    {
        $token = new UserSpotifyToken;
        $token->user_id = $user->id;
        $token->access_token = 'original';
        $token->refresh_token = 'original-refresh';
        $token->save();
    }
}
