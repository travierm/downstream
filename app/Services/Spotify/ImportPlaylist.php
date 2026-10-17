<?php

namespace App\Services\Spotify;

use SpotifyWebAPI\SpotifyWebAPI;

class ImportPlaylist
{
    public function __construct(private SpotifyWebAPI $api) {}

    public function find(string $userId): ?object
    {
        $offset = 0;
        do {
            $page = $this->api->getUserPlaylists($userId, ['limit' => 50, 'offset' => $offset]);
            foreach ($page->items as $playlist) {
                if (trim($playlist->name) === 'DS Import') {
                    return $playlist;
                }
            }
            $offset += count($page->items);
        } while (! empty($page->next) && count($page->items) > 0);

        return null;
    }

    public function tracks(string $playlistId): iterable
    {
        $offset = 0;
        do {
            $page = $this->api->getPlaylistTracks($playlistId, ['limit' => 50, 'offset' => $offset]);
            yield from $page->items;
            $offset += count($page->items);
        } while (! empty($page->next) && count($page->items) > 0);
    }
}
