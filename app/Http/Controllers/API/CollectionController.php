<?php

namespace App\Http\Controllers\API;

use DB;
use Auth;
use App\Models\Media;
use App\Models\MediaMeta;
use App\Models\UserMedia;
use App\Models\GlobalQueue;
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

        //build media collection list
        $collection = [];
        foreach($items as $media) {

            if($media->meta) {
                $media->meta = json_decode($media->meta);

                $mediaMeta = MediaMeta::where('media_id', $media->media_id)->first();
                
                if(@$mediaMeta->thumbnail_colors) {
                    $media->meta->thumbnailColors = $mediaMeta->thumbnail_colors;
                }

                //collected will always be true
                $media->collected = true;
                $media->globalQueued = GlobalQueue::mediaIsQueued($media->id);
            }

            $collection[] = $media;
        }

        return response()->json([
            'items' => $collection
        ], 200);
    }
}
