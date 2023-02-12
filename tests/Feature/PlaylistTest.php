<?php

namespace Tests\Feature;

use App\Models\Playlist;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\UserData;

global $user;

class PlaylistTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        global $user;

        parent::setUp();
        $this->setUpFaker();

        if (! $user) {
            $user = User::factory()->make();
        }

        UserData::setUser($user->id);
    }

    // Create list
    public function testCanCreateList()
    {
        global $user;

        $response = $this->actingAs($user)->post('/api/playlist/create', [
            'name' => $this->faker->city,
        ]);

        $playlistId = $response->json()['playlist_id'];

        $response->assertStatus(200);
        $this->assertNotEmpty($playlistId, 'Playlist ID is returned from playlist/create');

        return $playlistId;
    }

    /**
     * @depends testCanCreateList
     */
    public function testCanGetAllLists($playlistId)
    {
        global $user;

        $response = $this->actingAs($user)->get('/api/playlist/all');
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $playlistId], 'Created playlist_id exists in /playlist/all data');
    }

    /**
     * @group youtube
     * @depends testCanCreateList
     */
    public function testCanAddAndRemoveItemFromList($playlistId)
    {
        global $user;

        $media = UserData::getFirstCollectedItem();

        // Add media to playlist
        $response = $this->actingAs($user)->post('/api/playlist/add', [
            'media_id' => $media->id,
            'playlist_id' => $playlistId,
        ]);
        $response->assertStatus(200);

        // See media in playlist
        $response = $this->actingAs($user)->get('/api/playlist/'.$playlistId);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $media->id,
        ]);

        // See media has itemAdded true when viewing all playlists
        $mediaParam = '?media_id='.$media->id;
        $response = $this->actingAs($user)->get('/api/playlist/all'.$mediaParam);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'itemAdded' => true,
        ]);

        // Delete media from playlist
        $route = '/api/playlist/'.$playlistId.'/delete/'.$media->id;
        $response = $this->actingAs($user)->delete($route);
        $response->assertStatus(200);

        // Can not see media in playlist
        $response = $this->actingAs($user)->get('/api/playlist/'.$playlistId);
        $response->assertStatus(200);
        $response->assertJsonMissing([
            'id' => $media->id,
        ]);
    }

    /**
     * @depends testCanCreateList
     */
    public function testCanDeleteList($playlistId)
    {
        global $user;

        $response = $this->actingAs($user)->delete('/api/playlist/delete/'.$playlistId);
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get('/api/playlist/all');
        $response->assertStatus(200);
        $response->assertJsonMissing(['id' => $playlistId], 'Deleted playlist_id does not exists in /playlist/all data');
    }
}
