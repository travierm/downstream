<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    private $shouldCacheCollection = false;

    public function getCollection(Request $request)
    {
        $lc = getRequestLogContext();

        $userId = Auth::user()->id;
        $playlistId = $request->get('playlist_id');

        if (! $userId) {
            return response()->json([
                'code' => 401,
                'message' => 'No UserID was found',
            ], 401);
        } else {
            $collectionCacheKey = 'user_collection_items_' . $userId;
            $itemHashCacheKey = 'user_collection_items_hash_' . $userId;
        }

        $lc->info('fetching user collection', [
            'should_cache_collection' => $this->shouldCacheCollection,
            'playlist_id' => $playlistId,
        ]);

        if ($this->shouldCacheCollection && ! $playlistId) {
            if (Cache::has($collectionCacheKey) && Cache::has($itemHashCacheKey)) {
                $items = Cache::get($collectionCacheKey);
                $itemsHash = Cache::get($itemHashCacheKey);

                return response()->json([
                    'hash' => $itemsHash,
                    'items' => $items,
                ], 200);
            }
        }

        $queryBuilder = DB::table('media')
            ->join('user_media', 'user_media.media_id', '=', 'media.id')
            ->where('user_media.user_id', $userId)
            ->whereNull('user_media.deleted_at');

        if ($playlistId) {
            $queryBuilder->join('playlist_items', 'playlist_items.media_id', '=', 'media.id');
            $queryBuilder->where('playlist_items.created_by', $userId);
            $queryBuilder->where('playlist_items.playlist_id', $playlistId);
            $queryBuilder->whereNull('playlist_items.deleted_at');
        }

        // Add limit if needed for mobile
        if ($request->limit) {
            $queryBuilder->limit($request->limit);
        }

        if ($request->randomized) {
            $queryBuilder->orderBy('created_at', 'DESC');
        } else {
            $queryBuilder->orderBy('user_media.pushed_at', 'DESC');
        }

        $items = $queryBuilder->get();
        if($request->randomized) {
            $items = $items->shuffle();

            // Chunk the groups into sizes of 10 and shuffle each chunk
            $shuffledChunks = $items->map(function ($group) {
                return $group->chunk(10);
            })->values()->all();

            // Shuffle the chunks
            shuffle($shuffledChunks);

            // Flatten the array
            $items = collect($shuffledChunks)->flatten(1)->all();
        }

        $lc->info(sprintf('fetched %s media items from database', count($items)));

        $collection = [];
        foreach ($items as $media) {
            // items in collection will always be collected
            $media->collected = true;
            $media->guid = 'guid_' . Str::random(35);

            $collection[] = $media;
        }

        $mediaIds = array_map(function ($item) {
            return $item->id;
        }, $collection);

        $collectionItemsHash = md5(serialize($mediaIds));

        if ($this->shouldCacheCollection) {
            Cache::put($collectionCacheKey, $collection);
            Cache::put($itemHashCacheKey, $collectionItemsHash);
        }

        return response()->json([
            'hash' => $collectionItemsHash,
            'items' => $collection,
        ], 200);
    }

    public function getCollectionByHash(Request $request)
    {
        $viewUser = Auth::user();
        $userId = $viewUser->id;
        $userHash = $request->userHash;
        $isViewingSelf = $userHash == $viewUser->hash;

        if (! $userId || ! $userHash) {
            return response()->json([
                'code' => 401,
                'message' => 'No UserID was found',
            ], 401);
        }

        if ($userHash && ! $isViewingSelf) {
            $viewUser = User::select('id', 'display_name', 'hash')->where('hash', $userHash)->first();
            $userId = $viewUser->id;
        }

        $items = DB::table('media')
            ->join('user_media', 'user_media.media_id', '=', 'media.id')
            ->where('user_media.user_id', $userId)
            ->whereNull('user_media.deleted_at')
            ->limit(20)
            ->orderBy('user_media.pushed_at', 'DESC')
            ->get();

        $collection = [];
        foreach ($items as $media) {
            // items in collection will always be collected
            if ($isViewingSelf) {
                $media->collected = true;
            }
            $media->guid = 'guid_' . Str::random(35);

            if ($isViewingSelf) {
                $collection[] = $media;
            } else {
                $result = new \stdClass();
                $result->id = $media->id;
                $result->media_id = $media->media_id;
                $result->title = $media->title;
                $result->thumbnail = $media->thumbnail;
                $result->type = $media->type;
                $result->guid = $media->guid;
                $result->index = $media->index;

                $collection[] = $result;
            }
        }

        $mediaIds = array_map(function ($item) {
            return $item->id;
        }, $collection);

        $collectionItemsHash = md5(serialize($mediaIds));

        return response()->json([
            'hash' => $collectionItemsHash,
            'items' => $collection,
            'user' => $viewUser,
        ], 200);
    }
}
