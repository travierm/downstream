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

    public function test_can_get_play_count()
    {
        $response = $this->get('/api/user/stats');
        $response->assertStatus(200);
    }

     public function test_can_get_collection_count()
     {
         $response = $this->get('/api/user/stats');
         $response->assertStatus(200);
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
