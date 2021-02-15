<?php

namespace App\Http\Controllers\API;


use DB;
use Auth;
use Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionController extends Controller
{
    private $shouldCacheCollection = false;

    public function getCollection(Request $request) 
    {
        $userId = Auth::user()->id;

        if(!$userId) {
            return response()->json([
                'code'      =>  401,
                'message'   =>  "No UserID was found"
              ], 401);
        }else{
            $collectionCacheKey  = 'user_collection_items_' . $userId;
            $itemHashCacheKey = 'user_collection_items_hash_' . $userId;
        }

        if($this->shouldCacheCollection) {
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
