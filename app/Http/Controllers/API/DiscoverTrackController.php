<?php

namespace App\Http\Controllers\API;

use App\Models\Media;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\Discovery\SimilarTracks;

class DiscoverTrackController extends Controller
{
    public function getDailyMix()
    {
        $dailyMixCache = Cache::get('daily-mix-' . Auth::user()->id, null);
        if(!$dailyMixCache) {
            return response()->json([
                'message' => "Daily Mix has not been generated for you yet. Make sure you've collected atleast one track this week."
            ], 404);
        }

        return response()->json($dailyMixCache, 200);
    }

    public function similarTracks($videoId)
    {
        $lc = getRequestLogContext();
        $media = Media::where('index', $videoId)->first();
        $lc->info('similarTracks request started', [
            'videoId' => $videoId,
            'mediaId' => $media?->id,
        ]);

        if (! $media) {
            return response()->json([
                'message' => 'Bad video id given',
            ], 500);
        }

        $items = [];
        try {
            $items = SimilarTracks::similarTracksByMedia($media);
            array_unshift($items, $media);

            $lc->info(sprintf('found %s similar tracks', count($items)));
        } catch(\Exception $e) {
            throw $e;

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json(['items' => $items], 200);
    }
}
