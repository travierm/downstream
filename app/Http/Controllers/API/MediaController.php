<?php

namespace App\Http\Controllers\API;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Services\YoutubeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

    public function autofixByMediaId(string $id)
    {
        $userType = Auth::user()->type;
        if($userType !== 'admin') {
            return response()->json([
                'message' => 'Only admins can autofix videos',
            ], 403);
        }

        $media = Media::find($id);
        if(!$media) {
            return response()->json([
                'message' => 'Unable to find media by id',
                'id' => $id,
            ], 404);
        }

        try {
            YoutubeService::fixBrokenVideo($media);
        } catch(\Exception $e) {
            return response()->json([
                'message' => 'Unable to autofix video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
