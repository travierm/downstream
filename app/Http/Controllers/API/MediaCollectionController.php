<?php

namespace App\Http\Controllers\API;

use Cache;
use App\Models\Media;
use App\Models\UserMedia;
use Illuminate\Http\Request;
use App\Services\YoutubeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MediaCollectionController extends Controller
{
    public function pushItem(Int $mediaId) {
        $userId = Auth::user()->id;

        $item = UserMedia::where('user_id', $userId)
            ->where('media_id', $mediaId)
            ->first();

        if($item) {
            $item->pushed_at = now();
            $item->save();

            return response()->json([
                'mediaId' => $mediaId,
                'message' => 'Updated item pushed_at date'
            ], 200);
        }

        return response()->json([
            'mediaId' => $mediaId,
            'message' => 'Failed to update pushed_at date on item'
        ], 500);
    }

    /**
     * Logic:
     * - Check if videoId is already a media item
     * - if yes then add to users collection
     * - if no then create media item and add to users collection
     */
    public function postCollectItem(Request $request)
    {

        $userId = Auth::user()->id;
        $videoId = $request->videoId;

        if(!$userId || !$videoId) {
            return response()->json([
                'message' => 'userId or videoId not available',
                'userId' => $userId,
                'videoId' => $videoId
            ], 500);
        }

        $media = Media::where('index', $videoId)->first();

        if(!$media) {
            $video = YoutubeService::getVideoById($videoId);

            // runs another check to make sure media exists
            $media = Media::createFromYoutubeVideo($video, [
                'user_id' => $userId
            ]);

            if(!$media) {
                return response()->json([
                    'message' => 'Failed to create media item'
                ], 500);
            }
        }

        $userMediaId = UserMedia::firstOrCreate([
            'media_id' => $media->id,
            'user_id' => $userId
        ]);

        // Add mediaId to users collection
        if(!$userMediaId) {
            return response()->json([
                'message' => 'Failed to add media to users collection'
            ], 500);
        }

        $this->clearCollectionCache($userId);

        return response()->json([
            'mediaId' => $media->id,
            'message' => 'successfully collected media item'
        ], 200);
    }

    public function removeItemFromCollection(Int $itemId) 
    {
        $userId = Auth::user()->id;

        UserMedia::where('user_id', $userId)
            ->where('media_id', $itemId)
            ->delete();

        $this->clearCollectionCache($userId);

        return response()->json([
            'message' => 'removed item from collection'
        ], 200);
    }

    private function clearCollectionCache($userId)
    {
        $itemHashCacheKey = 'user_collection_items_hash_' . $userId;
        $collectionCacheKey  = 'user_collection_items_' . $userId;
        
        Cache::forget($itemHashCacheKey);
        Cache::forget($collectionCacheKey);
    }
}
