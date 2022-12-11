<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function getVideoByIndex(Request $request)
    {
        $videoId = $request->videoId;
        $mediaItem = Media::where('index', $videoId)->first();

        if (! $mediaItem) {
            return response()->json([
                'message' => 'Unable to find media by video_id',
                'videoId' => $videoId,
            ], 500);
        }

        return response()->json([
            'item' => $mediaItem,
        ], 200);
    }
}
