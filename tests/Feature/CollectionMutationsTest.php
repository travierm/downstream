<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Models\UserMedia;
use Illuminate\Support\Facades\Cache;
use Madcoda\Youtube\Facades\Youtube;
use Tests\TestCase;

class CollectionMutationsTest extends TestCase
{
    public function test_collecting_a_new_video_persists_media_metadata_and_only_the_authenticated_users_collection(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Youtube::shouldReceive('getVideoInfo')->once()->with('new-video')->andReturn((object) [
            'id' => 'new-video',
            'snippet' => (object) [
                'title' => 'Artist - Song [Official Video]',
                'thumbnails' => (object) ['high' => (object) ['url' => 'https://example.test/cover.jpg']],
            ],
        ]);

        $response = $this->actingAs($user)->postJson('/api/media/collect', [
            'videoId' => 'new-video', 'user_id' => $other->id,
        ])->assertOk();
        $media = Media::findOrFail($response->json('mediaId'));
        $this->assertSame('Artist - Song', $media->title);
        $this->assertSame($media->title, $media->meta->title);
        $this->assertSame('https://example.test/cover.jpg', $media->meta->thumbnail);
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseCount('user_media', 1);
        $this->assertDatabaseHas('user_media', ['user_id' => $user->id, 'media_id' => $media->id]);
        $this->getJson('/api/collection')->assertOk()->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.media_id', $media->id);
    }

    public function test_collecting_existing_media_twice_is_idempotent_and_does_not_call_youtube(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $user->id]);
        UserMedia::factory()->create(['user_id' => $other->id, 'media_id' => $media->id]);
        Youtube::shouldReceive('getVideoInfo')->never();

        $this->actingAs($user);
        foreach (range(1, 2) as $attempt) {
            $this->postJson('/api/media/collect', ['videoId' => $media->index])
                ->assertOk()->assertJsonPath('mediaId', $media->id);
        }
        $this->assertDatabaseCount('media', 1);
        $this->assertSame(1, UserMedia::where('user_id', $user->id)->count());
        $this->assertSame(1, UserMedia::where('user_id', $other->id)->count());
    }

    public function test_removing_and_recollecting_restores_the_item_at_the_top_without_duplicate_rows(): void
    {
        $this->travelTo(now()->startOfSecond());
        $user = User::factory()->create();
        $item = UserMedia::factory()->create(['user_id' => $user->id, 'pushed_at' => now()->subDays(2)]);
        $newer = UserMedia::factory()->create(['user_id' => $user->id, 'pushed_at' => now()->subDay()]);
        $this->actingAs($user)->deleteJson('/api/media/collection/'.$item->media_id)->assertOk();
        $this->assertSoftDeleted($item);
        $this->getJson('/api/collection')->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.media_id', $newer->media_id);

        $this->postJson('/api/media/collect', ['videoId' => $item->media->index])->assertOk();
        $this->assertFalse($item->fresh()->trashed());
        $this->assertSame(1, UserMedia::withTrashed()->where('user_id', $user->id)->where('media_id', $item->media_id)->count());
        $this->getJson('/api/collection')->assertJsonCount(2, 'items')
            ->assertJsonPath('items.0.media_id', $item->media_id);
    }

    public function test_removing_shared_media_only_removes_the_current_users_copy(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $item = UserMedia::factory()->create(['user_id' => $owner->id]);
        $otherItem = UserMedia::factory()->create(['user_id' => $other->id, 'media_id' => $item->media_id]);

        $this->actingAs($owner)->deleteJson('/api/media/collection/'.$item->media_id)->assertOk();
        $this->assertSoftDeleted($item);
        $this->assertFalse($otherItem->fresh()->trashed());
        $this->assertNotNull(Media::find($item->media_id));
        $this->getJson('/api/collection')->assertJsonCount(0, 'items');
    }

    public function test_another_user_cannot_remove_or_push_an_owners_item(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $item = UserMedia::factory()->create(['user_id' => $owner->id, 'pushed_at' => now()->subDay()]);
        $before = $item->fresh()->getAttributes();

        $this->actingAs($other)->deleteJson('/api/media/collection/'.$item->media_id)->assertOk();
        $this->postJson('/api/media/push/'.$item->media_id)->assertNotFound();
        $this->assertSame($before, $item->fresh()->getAttributes());
        $this->getJson('/api/collection')->assertJsonCount(0, 'items');
    }

    public function test_pushing_a_track_reorders_only_the_current_users_collection(): void
    {
        $this->travelTo(now()->startOfSecond());
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $item = UserMedia::factory()->create(['user_id' => $owner->id, 'pushed_at' => now()->subDays(2)]);
        $newer = UserMedia::factory()->create(['user_id' => $owner->id, 'pushed_at' => now()->subDay()]);
        $otherItem = UserMedia::factory()->create(['user_id' => $other->id, 'media_id' => $item->media_id, 'pushed_at' => now()->subDays(3)]);
        $otherTimestamp = $otherItem->fresh()->pushed_at;

        $this->actingAs($owner)->getJson('/api/collection')->assertJsonPath('items.0.media_id', $newer->media_id);
        $this->postJson('/api/media/push/'.$item->media_id)->assertOk();
        $this->assertSame(now()->toDateTimeString(), $item->fresh()->pushed_at);
        $this->assertSame($otherTimestamp, $otherItem->fresh()->pushed_at);
        $this->getJson('/api/collection')->assertJsonPath('items.0.media_id', $item->media_id);
    }

    public function test_collection_changes_clear_only_the_current_users_cached_collection(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $media = Media::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        foreach (['collect', 'push', 'remove'] as $operation) {
            foreach ([$user, $other] as $account) {
                Cache::put('user_collection_items_'.$account->id, ['cached']);
                Cache::put('user_collection_items_hash_'.$account->id, 'cached-hash');
            }
            match ($operation) {
                'collect' => $this->postJson('/api/media/collect', ['videoId' => $media->index])->assertOk(),
                'push' => $this->postJson('/api/media/push/'.$media->id)->assertOk(),
                'remove' => $this->deleteJson('/api/media/collection/'.$media->id)->assertOk(),
            };
            $this->assertNull(Cache::get('user_collection_items_'.$user->id), $operation);
            $this->assertNull(Cache::get('user_collection_items_hash_'.$user->id), $operation);
            $this->assertSame(['cached'], Cache::get('user_collection_items_'.$other->id));
            $this->assertSame('cached-hash', Cache::get('user_collection_items_hash_'.$other->id));
        }
    }
}
