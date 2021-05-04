<?php

namespace App\Http\Controllers\API;

use App\Models\Media;
use App\Http\Controllers\Controller;
use App\Services\Discovery\SimilarTracks;

use Illuminate\Http\Request;

class DiscoverTrackController extends Controller
{
    public function similarTracks($videoId)
    {
        $media = Media::where('index', $videoId)->first();

        if(!$media) {
            return response()->json([
                'message' => 'Bad video id given'
            ], 500);
        }

        $items = [];
        try {
           $items = SimilarTracks::similarTracksByMedia($media);
        } catch(\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json(['items' => $items], 200);
    }
}
