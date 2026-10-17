<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use App\Services\Discovery\SimilarTracks;
use Cumulati\Monolog\LogContext;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DailyMixService
{
    public function getDailyMix(User $user): array
    {
        $key = 'daily-mix-'.$user->id;
        $cached = Cache::get($key);
        if ($cached !== null) {
            return $cached;
        }

        return Cache::lock($key.':lock', 1200)->block(65, function () use ($key, $user) {
            return Cache::get($key) ?? $this->generateDailyMix($user);
        });
    }

    public function generateDailyMix(User $user): array
    {
        $lc = new LogContext(['user_id' => $user->id]);

        $existingIgnoreIds = Cache::get('daily-mix-ignore-'.$user->id, []);
        $lc->debug('existing ignore ids', ['ids' => $existingIgnoreIds]);

        $seedIds = $this->getSeedMediaIds($user, $existingIgnoreIds);
        $lc->info('using seed ids', ['ids' => $seedIds]);
        $mediaItems = Media::whereIn('id', $seedIds)->get();

        $videos = collect();
        foreach ($mediaItems as $item) {
            $tracks = SimilarTracks::similarTracksByMedia($item, 6);
            $videos = $videos->merge($tracks);
        }
        $lc->info('found similar tracks', ['count' => $videos->count()]);

        $mix = ['videos' => $videos->values()->all()];
        if ($mediaItems->isEmpty()) {
            // Let collecting a first track immediately make the user eligible.
            return $mix;
        }

        Cache::put('daily-mix-'.$user->id, $mix, $videos->isEmpty() ? now()->addMinutes(5) : now()->addDay());
        if ($videos->isNotEmpty()) {
            Cache::put('daily-mix-ignore-'.$user->id, array_values(array_unique(array_merge($existingIgnoreIds, $seedIds))), now()->addMonth());
        }

        return $mix;
    }

    private function getSeedMediaIds(User $user, array $ignoredIds = []): array
    {
        // Prefer recent listening plus up to three popular older tracks.

        $itemsCollectedThisWeek = $user->userMediaPlays()
            ->select('media_id', DB::raw('count(*) as plays'))
            ->whereDate('created_at', '>=', now()->subWeek())
            ->whereNotIn('media_id', $ignoredIds)
            ->whereIn('media_id', Media::select('id'))
            ->groupBy('media_id')
            ->inRandomOrder()
            ->limit(3)
            ->pluck('media_id');

        $itemsCollectedAMonthOrMoreAgo = $user->userMediaPlays()
            ->select('media_id', DB::raw('count(*) as plays'))
            ->whereDate('created_at', '<', now()->subMonth())
            ->whereNotIn('media_id', $ignoredIds)
            ->whereIn('media_id', Media::select('id'))
            ->groupBy('media_id')
            ->orderBy('plays', 'desc')
            ->limit(50)
            ->pluck('media_id');

        $allItems = $itemsCollectedThisWeek->merge(
            $itemsCollectedAMonthOrMoreAgo->random(min(3, $itemsCollectedAMonthOrMoreAgo->count()))
        )->unique()->values();

        if ($allItems->isEmpty()) {
            $allItems = $user->media()->whereHas('media')
                ->whereNotIn('media_id', $ignoredIds)
                ->latest()->limit(3)->pluck('media_id');
        }
        // Small libraries should still get a mix when all available seeds were used.
        if ($allItems->isEmpty() && count($ignoredIds)) {
            return $this->getSeedMediaIds($user);
        }

        return $allItems->all();
    }
}
