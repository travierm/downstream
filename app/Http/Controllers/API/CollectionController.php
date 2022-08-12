<?php

namespace App\Http\Controllers\API;


use DB;
use Auth;
use Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class CollectionController extends Controller
{
    private $shouldCacheCollection = false;

    public function getCollection(Request $request)
    {
        $userHash = $request->userHash;
        $userId = Auth::user()->id;
        if ($userHash) {
            $userId = User::select('id', 'display_name')->where('hash', $userHash)->first()->id;
        }

        $playlistId = $request->get('playlist_id');

        if(!$userId) {
            return response()->json([
                'code'      =>  401,
                'message'   =>  "No UserID was found"
              ], 401);
        }else{
            $collectionCacheKey  = 'user_collection_items_' . $userId;
            $itemHashCacheKey = 'user_collection_items_hash_' . $userId;
        }

        if($this->shouldCacheCollection && !$playlistId) {
            if (Cache::has($collectionCacheKey) && Cache::has($itemHashCacheKey)) {
                $items = Cache::get($collectionCacheKey);
                $itemsHash = Cache::get($itemHashCacheKey);

                return response()->json([
                    'hash' => $itemsHash,
                    'items' => $items
                ], 200);
            }
        }

        $queryBuilder = DB::table('media')
            ->join('user_media', 'user_media.media_id', '=', 'media.id')
            ->where('user_media.user_id', $userId)
            ->whereNull('user_media.deleted_at');

        if($playlistId) {
            $queryBuilder->join('playlist_items', 'playlist_items.media_id', '=', 'media.id');
            $queryBuilder->where('playlist_items.created_by', $userId);
            $queryBuilder->where('playlist_items.playlist_id', $playlistId);
            $queryBuilder->whereNull('playlist_items.deleted_at');
        }

        // Add limit if needed for mobile
        if($request->limit) {
            $queryBuilder->limit($request->limit);
        }

        if($request->randomized) {
            $queryBuilder->orderByRaw("RAND()");
        }else{
            $queryBuilder->orderBy('user_media.pushed_at', 'DESC');
        }

        $items = $queryBuilder->get();

        $collection = [];
        foreach($items as $media) {
            // items in collection will always be collected
            $media->collected = true;
            $media->guid = "guid_" . Str::random(35);

            $collection[] = $media;
        }

        $mediaIds = array_map(function($item) {
            return $item->id;
        }, $collection);

        $collectionItemsHash = md5(serialize($mediaIds));

        if($this->shouldCacheCollection) {
            Cache::put($collectionCacheKey, $collection);
            Cache::put($itemHashCacheKey, $collectionItemsHash);
        }

        return response()->json([
            'hash' => $collectionItemsHash,
            'items' => $collection
        ], 200);
    }

    public function getCollectionByHash(Request $request)
    {
        $userHash = $request->userHash;
        $userId = Auth::user()->id;
        if ($userHash) {
            $userId = DB::table('user')->where('user.hash', $userHash)->get()->id;
        }

        $playlistId = $request->get('playlist_id');

        if(!$userId) {
            return response()->json([
                'code'      =>  401,
                'message'   =>  "No UserID was found"
              ], 401);
        }else{
            $collectionCacheKey  = 'user_collection_items_' . $userId;
            $itemHashCacheKey = 'user_collection_items_hash_' . $userId;
        }

        if($this->shouldCacheCollection && !$playlistId) {
            if (Cache::has($collectionCacheKey) && Cache::has($itemHashCacheKey)) {
                $items = Cache::get($collectionCacheKey);
                $itemsHash = Cache::get($itemHashCacheKey);

                return response()->json([
                    'hash' => $itemsHash,
                    'items' => $items
                ], 200);
            }
        }

        $queryBuilder = DB::table('media')
            ->join('user_media', 'user_media.media_id', '=', 'media.id')
            ->where('user_media.user_id', $userId)
            ->whereNull('user_media.deleted_at');

        if($playlistId) {
            $queryBuilder->join('playlist_items', 'playlist_items.media_id', '=', 'media.id');
            $queryBuilder->where('playlist_items.created_by', $userId);
            $queryBuilder->where('playlist_items.playlist_id', $playlistId);
            $queryBuilder->whereNull('playlist_items.deleted_at');
        }

        // Add limit if needed for mobile
        if($request->limit) {
            $queryBuilder->limit($request->limit);
        }

        if($request->randomized) {
            $queryBuilder->orderByRaw("RAND()");
        }else{
            $queryBuilder->orderBy('user_media.pushed_at', 'DESC');
        }

        $items = $queryBuilder->get();

        $collection = [];
        foreach($items as $media) {
            // items in collection will always be collected
            $media->collected = true;
            $media->guid = "guid_" . Str::random(35);

            $collection[] = $media;
        }

        $mediaIds = array_map(function($item) {
            return $item->id;
        }, $collection);

        $collectionItemsHash = md5(serialize($mediaIds));

        if($this->shouldCacheCollection) {
            Cache::put($collectionCacheKey, $collection);
            Cache::put($itemHashCacheKey, $collectionItemsHash);
        }

        return response()->json([
            'hash' => $collectionItemsHash,
            'items' => $collection
        ], 200);
    }
}
