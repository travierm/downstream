<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Models\UserMedia;
use App\Models\UserMediaPlays;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_dashboard_requires_an_admin()
    {
        $this->getJson('/api/admin/dashboard')->assertUnauthorized();
        $this->actingAs(User::factory()->create())
            ->getJson('/api/admin/dashboard')->assertForbidden();
    }

    public function test_dashboard_reports_totals_and_recent_activity_without_account_secrets()
    {
        $this->travelTo(now()->setDate(2026, 10, 17)->setTime(12, 0));
        $admin = User::factory()->create(['type' => 'admin', 'created_at' => now()->subDays(30)]);
        $user = User::factory()->create(['private' => true]);
        $song = Media::factory()->create(['title' => 'A new discovery', 'user_id' => $user->id]);
        UserMedia::factory()->create(['user_id' => $admin->id, 'media_id' => $song->id, 'created_at' => now()->subDays(20)]);
        $recent = UserMedia::factory()->create(['user_id' => $user->id, 'media_id' => $song->id]);
        $removed = UserMedia::factory()->create(['user_id' => $user->id]);
        $removed->delete();
        $missingSong = UserMedia::factory()->create(['user_id' => $user->id]);
        $missingSong->media->delete();
        UserMediaPlays::factory()->create(['user_id' => $user->id, 'media_id' => $song->id]);
        UserMediaPlays::factory()->create(['user_id' => $user->id, 'media_id' => $song->id, 'created_at' => now()->subDays(8)]);
        UserMediaPlays::factory()->create(['user_id' => $user->id, 'media_id' => $song->id, 'created_at' => now()->subDays(14)]);

        $response = $this->actingAs($admin)->getJson('/api/admin/dashboard')->assertOk();
        $response->assertJsonPath('stats.users', 2)
            ->assertJsonPath('stats.new_users_week', 1)
            ->assertJsonPath('stats.collections', 2)
            ->assertJsonPath('stats.collections_week', 1)
            ->assertJsonPath('stats.unique_songs', 1)
            ->assertJsonPath('stats.active_collectors_week', 1)
            ->assertJsonPath('stats.plays_week', 1)
            ->assertJsonCount(14, 'activity')
            ->assertJsonPath('activity.0.date', '2026-10-04')
            ->assertJsonPath('activity.0.collections', 0)
            ->assertJsonPath('activity.0.plays', 0)
            ->assertJsonPath('activity.5.plays', 1)
            ->assertJsonPath('activity.13.plays', 1)
            ->assertJsonPath('activity.13.collections', 1)
            ->assertJsonPath('activity.13.users', 1)
            ->assertJsonPath('recent_users.0.id', $user->id)
            ->assertJsonPath('recent_users.0.collection_count', 1)
            ->assertJsonPath('recent_collections.0.id', $recent->id)
            ->assertJsonPath('recent_collections.0.title', 'A new discovery')
            ->assertJsonCount(2, 'recent_collections');
        $this->assertEqualsCanonicalizing(
            ['id', 'display_name', 'type', 'created_at', 'collection_count'],
            array_keys($response->json('recent_users.0'))
        );
    }

    public function test_dashboard_limits_feeds_and_handles_no_collections()
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $this->actingAs($admin)->getJson('/api/admin/dashboard')->assertOk()
            ->assertJsonPath('stats.collections', 0)->assertJsonCount(0, 'recent_collections');
        User::factory()->count(10)->create();
        UserMedia::factory()->count(12)->create(['user_id' => $admin->id]);
        $this->getJson('/api/admin/dashboard')->assertOk()
            ->assertJsonCount(8, 'recent_users')->assertJsonCount(10, 'recent_collections');
    }
}
