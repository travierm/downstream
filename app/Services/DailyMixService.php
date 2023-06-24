<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\Discovery\SimilarTracks;
use Cumulati\Monolog\LogContext;

class DailyMixService
{
    public function generateDailyMix(User $user)
    {
        $lc = new LogContext(['user_id' => $user->id]);

        $existingIgnoreIds = Cache::get('daily-mix-ignore-'.$user->id, []);
        $lc->debug('existing ignore ids', ['ids' => $existingIgnoreIds]);

        $seedIds = $this->getSeedMediaIds($user, $existingIgnoreIds);
        $lc->info('using seed ids', ['ids' => $seedIds]);
        $mediaItems = Media::whereIn('id', $seedIds)->get();

        $videos = collect();
        foreach($mediaItems as $item) {
            $tracks = SimilarTracks::similarTracksByMedia($item, 6);
            $videos = $videos->merge($tracks);
        }
        $lc->info('found similar tracks', ['count' => $videos->count()]);

        Cache::put('daily-mix-'.$user->id, [
            'videos' => $videos,
        ], now()->addDays(1));

        Cache::put('daily-mix-ignore-'.$user->id, array_merge($existingIgnoreIds, $seedIds), now()->addMonths(1));
    }

    private function getSeedMediaIds(User $user, array $ignoredIds = []): array
    {
        // get 3 tracks collected this week order by play count
        // get one popular track from a month or longer ago
        // store all track IDS and cache them for 3 months to prevent repeats

        $itemsCollectedThisWeek = $user->userMediaPlays()
                ->select('media_id', DB::raw('count(*) as plays'))
                ->whereDate('created_at', '>=', now()->subWeek())
                ->whereNotIn('media_id', $ignoredIds)
                ->groupBy('media_id')
                ->inRandomOrder()
                ->limit(3)
                ->pluck('media_id');

        $itemsCollectedAMonthOrMoreAgo = $user->userMediaPlays()
                ->select('media_id', DB::raw('count(*) as plays'))
                ->whereDate('created_at', '<', now()->subMonth())
                ->whereNotIn('media_id', $ignoredIds)
                ->groupBy('media_id')
                ->orderBy('plays', 'desc')
                ->limit(50)
                ->pluck('media_id');

        $allItems = $itemsCollectedThisWeek->merge($itemsCollectedAMonthOrMoreAgo->random(3)->all());

        return $allItems->all();
    }
}
