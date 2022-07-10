<?php
namespace App\Repos;

use App\Models\UserSpotifyImport;
use Illuminate\Support\Facades\DB;

class SpotifyRepo
{
    public function createSpotifyImport(
        int $userId,
        string $trackName,
        string $trackArtist,
        string $searchQuery
    ) {
        return UserSpotifyImport::firstOrCreate([
            'user_id' => $userId,
            'track_name' => $trackName,
            'track_artist' => $trackArtist,
            'search_query' => $searchQuery
        ]);
    }

    public function setImported(UserSpotifyImport $import, int $mediaId): void
    {
        $import->imported_at = now();
        $import->imported = true;
        $import->media_id = $mediaId;

        $import->save();
    }

    public function getImportStats(int $userId): array
    {
        $period = now()->subMonths(12)->monthsUntil(now());

        $data = [];
        $categories = [];

        foreach ($period as $date) {
            $count = DB::table('user_spotify_imports')
                ->where('user_id', $userId)
                ->whereYear('created_at', '=', $date->year)
                ->whereMonth('created_at', '=', $date->month)
                ->count();

            $data[] = $count;
            $categories[] = $date->shortMonthName . ' ' . $date->year;
        }

        return [
            'data' => $data,
            'categories' => $categories
       ];
    }
}
