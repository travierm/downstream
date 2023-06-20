<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserMedia;
use App\Models\UserMediaPlays;

class UserStatsControllerTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_get_chart_data()
    {

        $userMedia = UserMedia::factory()->createOne([
            'user_id' => $this->user->id
        ]);

        $userMediaPlays = 5;
        UserMediaPlays::factory()->times($userMediaPlays)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/api/user/stats');
        $response->assertStatus(200);

        $this->assertEquals(5, $response->json('play_count_history')['data'][6]);
        $this->assertEquals(1, $response->json('collection_count_history')['data'][6]);
    }

    public function test_can_get_top_ten_tracks()
    {
        $firstUserMedia = UserMedia::factory()->createOne([
            'user_id' => $this->user->id
        ]);
        $secondUserMedia = UserMedia::factory()->createOne([
            'user_id' => $this->user->id
        ]);

        $firstUserMediaPlays = 5;
        UserMediaPlays::factory()->times($firstUserMediaPlays)->create([
            'user_id' => $this->user->id,
            'media_id' => $firstUserMedia->media_id
        ]);

        $secondUserMediaPlays = 2;
        UserMediaPlays::factory()->times($secondUserMediaPlays)->create([
            'user_id' => $this->user->id,
            'media_id' => $secondUserMedia->media_id
        ]);

        $response = $this->get('/api/user/stats');
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertEquals([
            'media_id' => $firstUserMedia->media_id,
            'title' => $firstUserMedia->media->title,
            'thumbnail' => $firstUserMedia->media->thumbnail,
            'plays' => $firstUserMediaPlays,
        ], $json['top_ten_tracks'][0]);

        $this->assertEquals([
            'media_id' => $secondUserMedia->media_id,
            'title' => $secondUserMedia->media->title,
            'thumbnail' => $secondUserMedia->media->thumbnail,
            'plays' => $secondUserMediaPlays,
        ], $json['top_ten_tracks'][1]);
    }
}
