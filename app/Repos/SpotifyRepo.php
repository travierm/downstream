<?php
namespace App\Repos;

use App\Models\UserSpotifyImport;

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
}
