<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\DailyMixService;
use App\Services\Discovery\SimilarTracks;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Auth;

class DiscoverTrackController extends Controller
{
    public function getDailyMix()
    {
        try {
            $mix = app(DailyMixService::class)->getDailyMix(Auth::user());
        } catch (LockTimeoutException $e) {
            return response()->json([
                'message' => 'Your Daily Mix is still being generated. Please try again shortly.',
            ], 503)->header('Retry-After', '5');
        }

        if (count($mix['videos']) === 0) {
            $mix['message'] = 'No Daily Mix is available yet. Collect or play some tracks, then try again.';
        }

        return response()->json($mix);
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
        } catch (LockTimeoutException $e) {
            return response()->json([
                'message' => 'Similar tracks are still being generated. Please try again shortly.',
            ], 503)->header('Retry-After', '5');
        }

        return response()->json(['items' => $items], 200);
    }
}
