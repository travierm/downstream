<?php

namespace App\Http\Controllers\API;

use DB;
use Auth;
use App\Models\Media;
use App\Models\MediaMeta;
use App\Models\UserMedia;
use App\Models\GlobalQueue;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionController extends Controller
{
    public function getCollection(Request $request) 
    {
        $userId = Auth::user()->id;

        if(!$userId) {
            return response()->json([
                'code'      =>  401,
                'message'   =>  "No UserID was found"
              ], 401);
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
            $queryBuilder->orderBy('user_media.id', 'DESC');
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

        return response()->json([
            'hash' => md5(serialize($mediaIds)),
            'items' => $collection
        ], 200);
    }
}
