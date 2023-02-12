<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use DB;
use Tests\Helper\MediaHelper;
use Tests\TestCase;

global $user;

class MediaCollectionTest extends TestCase
{
    public function setUp(): void
    {
        global $user;

        parent::setUp();

        if (! $user) {
            $user = User::factory()->make();
        }
    }

    public function testCanFetchUserCollection()
    {
        global $user;
        $response = $this->actingAs($user)->get('/api/collection');
        $response->assertStatus(200);
    }

    /**
     * @group youtube
     *
     * @return void
     */
    public function testCanCollectItem()
    {
        global $user;

        $testVideoId = 'lZcRSy0sk5w';

        MediaHelper::deleteByIndex($testVideoId);

        // Collect
        $response = $this->actingAs($user)->post('/api/media/collect', [
            // Kid Cudi - Tequila Shots
            'videoId' => 'lZcRSy0sk5w',
        ]);

        $mediaId = $response->json()['mediaId'];

        $this->assertNotEmpty($mediaId, 'Got mediaId back from route');
        $response->assertStatus(200);

        // Make sure meta gets set
        $media = Media::find($mediaId);
        $this->assertNotNull($media->meta, 'Media object has meta');
        $this->assertTrue($media->title === $media->meta->title, 'media->title matches media->meta->title');

        return $mediaId;
    }

    /**
     * @depends testCanCollectItem
     */
    public function testCanSeeCollectedItemInCollection($mediaId)
    {
        global $user;

        $response = $this->actingAs($user)->get('/api/collection/');
        $response->assertStatus(200);

        $collection = $response->json();
        if (! $collection) {
            dd($collection);
        }

        $response->assertJsonFragment([
            'media_id' => $mediaId,
        ]);

        return $mediaId;
    }

    /**
     * @depends testCanSeeCollectedItemInCollection
     */
    public function testCanRemoveItemFromCollection($mediaId)
    {
        global $user;

        // Get collection count
        $response = $this->actingAs($user)->get('/api/collection/');
        $response->assertStatus(200);

        $collection = $response->json();
        $collectionCount = count($collection['items']);

        $this->assertTrue($collectionCount >= 1, 'collection has more 1 or more items');

        // delete item from collection
        $response = $this->actingAs($user)->delete('/api/media/collection/'.$mediaId);
        $response->assertStatus(200);

        // assert collection count is one less
        $response = $this->actingAs($user)->get('/api/collection/');
        $response->assertStatus(200);

        $updatedCollection = $response->json();
        $updatedCollectionCount = count($updatedCollection['items']);

        $this->assertTrue($updatedCollectionCount == $collectionCount - 1, 'collect count is one less then before');
    }

    /**
     * @group youtube
     *
     * @return void
     */
    public function testCanSeeCollectedItemAtTopOfCollection()
    {
        global $user;

        DB::table('user_media')->where('user_id', $user->id)->delete();

        $this->actingAs($user)->post('/api/media/collect', [
            // Kid Cudi - Tequila Shots
            'videoId' => 'lZcRSy0sk5w',
        ])->assertStatus(200);

        sleep(1);

        $this->actingAs($user)->post('/api/media/collect', [
            // Drake - Laugh Now Cry Later
            'videoId' => 'JFm7YDVlqnI',
        ])->assertStatus(200);

        $response = $this->actingAs($user)->get('/api/collection/');
        $response->assertStatus(200);

        $collection = $response->json();

        $firstItemIndex = $collection['items'][0]['index'];
        $this->assertEquals('JFm7YDVlqnI', $firstItemIndex, 'last collected item is not at top of list');
    }
}
