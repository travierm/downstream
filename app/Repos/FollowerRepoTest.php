<?php

namespace App\Repos;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserMedia;
use App\Repos\FollowerRepo;

class FollowerRepoTest extends TestCase
{
    public function test_can_get_recently_collected_items()
    {
        $user = User::factory()->createOne();
        $followedUser = User::factory()->createOne();
        $followedUserMediaItems = UserMedia::factory()->times(3)->create([
            'user_id' => $followedUser->id,
        ]);

        $user->following()->attach($followedUser->id);

        $followerRepo = new FollowerRepo();
        $items = $followerRepo->getRecentlyCollectedItemsFromFollowing($user);

        $this->assertCount(3, $items);
    }
}
