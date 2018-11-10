<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function getMediaById($id) 
    {
        $item = Media::find($id);

        return $item;
    }

    public function postUpdate(Request $request) 
    {
        $userId = Auth::user()->id;
        $mediaId = $request->input('mediaId');

        if(!Auth::user()->type == "admin") {
            $media = Media::where('user_id', $userId)
                ->where('id', $mediaId)
                ->first();
        }else{
            echo "here $mediaId";
            $media = Media::where('id', $mediaId)
                ->first();
        }

        if(!$media) {
            return response()->json([
                'code' => 401,
                'message' => "Media not found or not owned by this user"
            ], 401);
        }

        $title = $request->input('title');
        $thumbnail = $request->input('thumbnail');
        //decode json
        $media->meta = json_decode($media->meta);

        if($title) {
            $media->meta->title = $title;
        }

        if($thumbnail) {
            $media->meta->thumbnail = $thumbnail;
        }

        $media->meta = json_encode($media->meta);
        $media->save();

        return response()->json([
            'code' => 200,
            'message' => "Saved media data"
        ]);
    }
}
