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
        $userId = Auth::user()->id;
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
        $viewUser = Auth::user();
        $userId = $viewUser->id;
        $userHash = $request->userHash;
        $isViewingSelf = $userHash == $viewUser->hash;

        if(!$userId || !$userHash) {
            return response()->json([
                'code'      =>  401,
                'message'   =>  "No UserID was found"
              ], 401);
        }

        if ($userHash && !$isViewingSelf) {
            $viewUser = User::select('id', 'display_name')->where('hash', $userHash)->first();
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
        foreach($items as $media) {
            // items in collection will always be collected
            if ($isViewingSelf) {
                $media->collected = true;
            }
            $media->guid = "guid_" . Str::random(35);


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

        $mediaIds = array_map(function($item) {
            return $item->id;
        }, $collection);

        $collectionItemsHash = md5(serialize($mediaIds));

        $simpleUser = new \stdClass();
        $simpleUser->display_name = $viewUser->display_name;

        return response()->json([
            'hash' => $collectionItemsHash,
            'items' => $collection,
            'user' => $simpleUser,
        ], 200);
    }
}
