<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Media;
use App\UserMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function getCollection($randomize = false) 
    {
        $userId = Auth::user()->id;

        if(!$userId) {
            return response()->json([
                'code'      =>  401,
                'message'   =>  "No UserID was found"
              ], 401);
        }

        //get mediaId's from users collection (user_media)
        $builder = UserMedia::where('user_id', $userId);
        if($randomize) {
            $builder->orderByRaw("RAND()");
        }else{
            $builder->orderBy('id', 'DESC');
        }
        $mediaIds = $builder->pluck('media_id');

        //build media collection list
        $collection = [];
        foreach($mediaIds as $id) {
            $media = Media::find($id);

            if($media->meta) {
                $media->meta = $media->getMeta();
                //collected will always be true
                $media->collected = true;
            }

            $collection[] = $media;
        }

        return response()->json([
            'items' => $collection
        ], 200);
    }
}
