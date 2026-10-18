<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use App\Services\Discovery\SimilarTracks;
use Cumulati\Monolog\LogContext;
use Illuminate\Support\Facades\Cache;

class DailyMixService
{
    public function getDailyMix(User $user): array
    {
        $key = 'daily-mix:v2:'.$user->id;
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

        $cooldownKey = 'daily-mix-cooldowns:v2:'.$user->id;
        $cooldowns = array_filter(Cache::get($cooldownKey, []), fn ($expiresAt) => $expiresAt > now()->timestamp);

        $seedIds = $this->getSeedMediaIds($user, array_keys($cooldowns));
        $lc->info('using seed ids', ['ids' => $seedIds]);
        $mediaItems = Media::whereIn('id', $seedIds)->get()
            ->sortBy(fn ($media) => array_search($media->id, $seedIds))->values();

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

        Cache::put('daily-mix:v2:'.$user->id, $mix, $videos->isEmpty() ? now()->addMinutes(5) : now()->addDay());
        if ($videos->isNotEmpty()) {
            foreach ($seedIds as $seedId) {
                $cooldowns[$seedId] = now()->addDays(3)->timestamp;
            }
            Cache::put($cooldownKey, $cooldowns, now()->addDays(3));
        }

        return $mix;
    }

    private function getSeedMediaIds(User $user, array $ignoredIds = []): array
    {
        $query = $user->media()->whereHas('media')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('media_id')
            ->groupBy('media_id')
            ->orderByRaw('MAX(created_at) DESC')
            ->orderByDesc('media_id')
            ->limit(6);

        $seedIds = (clone $query)->whereNotIn('media_id', $ignoredIds)->pluck('media_id');
        // Reuse seeds only when every eligible collection is on cooldown.
        if ($seedIds->isEmpty() && count($ignoredIds)) {
            $seedIds = $query->pluck('media_id');
        }

        return $seedIds->all();
    }
}
