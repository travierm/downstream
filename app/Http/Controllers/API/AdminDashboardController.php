<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMedia;
use App\Models\UserMediaPlays;
use Illuminate\Database\Eloquent\Builder;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();
        $weekStart = $today->copy()->subDays(6);
        $chartStart = $today->copy()->subDays(13);
        $collections = UserMedia::whereHas('media')->whereHas('user');

        $recentUsers = User::query()->select('id', 'display_name', 'type', 'created_at')
            ->withCount(['media' => fn (Builder $query) => $query->whereHas('media')])
            ->latest()->orderByDesc('id')->limit(8)->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'display_name' => $user->display_name,
                'type' => $user->type,
                'created_at' => $user->created_at?->toIso8601String(),
                'collection_count' => $user->media_count,
            ]);

        $recentCollections = (clone $collections)
            ->with(['user:id,display_name', 'media:id,title,thumbnail'])
            ->latest()->orderByDesc('id')->limit(10)->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'title' => $item->media->title,
                'thumbnail' => $item->media->thumbnail,
                'collected_by' => $item->user->display_name,
                'created_at' => $item->created_at?->toIso8601String(),
            ]);

        $signups = $this->dailyCounts(User::query(), $chartStart);
        $collects = $this->dailyCounts(clone $collections, $chartStart);
        $plays = $this->dailyCounts(UserMediaPlays::query(), $chartStart);
        $activity = [];
        for ($day = $chartStart->copy(); $day->lte($today); $day->addDay()) {
            $date = $day->toDateString();
            $activity[] = [
                'date' => $date,
                'users' => (int) ($signups[$date] ?? 0),
                'collections' => (int) ($collects[$date] ?? 0),
                'plays' => (int) ($plays[$date] ?? 0),
            ];
        }

        return response()->json([
            'stats' => [
                'users' => User::count(),
                'new_users_week' => User::where('created_at', '>=', $weekStart)->count(),
                'collections' => (clone $collections)->count(),
                'collections_week' => (clone $collections)->where('created_at', '>=', $weekStart)->count(),
                'unique_songs' => (clone $collections)->distinct()->count('media_id'),
                'active_collectors_week' => (clone $collections)->where('created_at', '>=', $weekStart)
                    ->distinct()->count('user_id'),
                'plays_week' => UserMediaPlays::where('created_at', '>=', $weekStart)->count(),
            ],
            'activity' => $activity,
            'recent_users' => $recentUsers,
            'recent_collections' => $recentCollections,
            'updated_at' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
        ]);
    }

    private function dailyCounts(Builder $query, $start)
    {
        return $query->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')->pluck('total', 'day');
    }
}
