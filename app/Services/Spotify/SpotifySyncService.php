<?php

namespace App\Services\Spotify;

use App\Loggers\SpotifyLogger;
use App\Models\Artist;
use App\Models\Media;
use App\Models\MediaMeta;
use App\Models\UserMedia;
use App\Models\UserSpotifyToken;
use App\Repos\SpotifyRepo;
use App\Services\SpotifyAPI;
use App\Services\YoutubeService;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifySyncService
{
    private SpotifyLogger $lc;

    public function __construct(public SpotifyRepo $spotifyRepo)
    {
        $this->lc = new SpotifyLogger([
            'group' => 'spotify@import',
        ]);
    }

    public function syncByToken(UserSpotifyToken $token): void
    {
        $spotifyApi = $this->apiForToken($token);

        $importPlaylist = $this->getOrCreateImportPlaylist($spotifyApi, $spotifyApi->me());
        if (! $importPlaylist) {
            return;
        }

        foreach ((new ImportPlaylist($spotifyApi))->tracks($importPlaylist->id) as $track) {
            if (! $track->track) {
                continue;
            }

            $successful = $this->syncTrack($token->user_id, $track);

            if ($successful) {
                $this->lc->info('imported spotify track');
            }
        }
    }

    protected function apiForToken(UserSpotifyToken $token): SpotifyWebAPI
    {
        return SpotifyAPI::getInstanceWithToken($token);
    }

    public function getOrCreateImportPlaylist(SpotifyWebAPI $spotifyApi, object $spotifyUser): ?object
    {
        $importList = (new ImportPlaylist($spotifyApi))->find($spotifyUser->id);

        // could not find existing playlist, create new and exit
        if (! $importList) {
            $spotifyApi->createPlaylist([
                'name' => 'DS Import',
            ]);

            $this->lc->info('created downstream import playlist');

            return null;
        }

        $this->lc->info('import playlist was found on spotify account');

        return $importList;
    }

    public function updatePlaylistImage($api, $playlist)
    {
        $spotifyUser = $api->me();
        $spotifyUserId = $spotifyUser->id;

        if (! $playlist->images) {
            $imageData = base64_encode(file_get_contents(public_path('android-chrome-192x192.png')));

            try {
                $success = $api->updatePlaylistImage($playlist->uri, $imageData);
                dd($success);
            } catch (\Exception $e) {
                echo 'Spotify API Error: '.$e->getCode(); // Will be 404
            }

            $this->lc->info('updated user playlist image');
        } else {
            dd($playlist);
        }
    }

    public function syncTrack(int $userId, object $track): bool
    {
        $trackId = $track->track->id ?? null;
        if (! $trackId) {
            $this->lc->info('skipping track without a Spotify ID');

            return false;
        }

        $trackName = $track->track->name;
        $trackArtist = $track->track->artists[0]->name;
        $trackSearchQuery = $trackArtist.' '.$trackName;

        $this->lc->info('attempting to import with query '.$trackSearchQuery);
        // Older imports may have the Spotify ID in only one of these tables.
        $media = Media::query()
            ->where('spotify_id', $trackId)
            ->orWhereHas('meta', fn ($query) => $query->where('spotify_id', $trackId))
            ->first();
        $importRecord = null;

        if (! $media) {
            $importRecord = $this->spotifyRepo->createSpotifyImport($userId, $trackName, $trackArtist, $trackSearchQuery);
            $videos = YoutubeService::searchByQuery($trackSearchQuery);
            if (empty($videos)) {
                $this->lc->info("no video found using search query $trackSearchQuery");

                return false;
            }

            $video = $videos[0];
            $media = Media::firstOrCreate([
                'type' => 'youtube',
                'index' => $video->videoId,
            ], [
                'origin' => 'spotify#import',
                'subtype' => 'video',
                'user_id' => $userId,
                'title' => $video->title,
                'thumbnail' => $video->thumbnail,
            ]);
        }

        if (! $media->spotify_id || $media->spotify_id !== $trackId) {
            $media->spotify_id = $trackId;
            $media->save();
        }

        $userMedia = UserMedia::firstOrCreate([
            'media_id' => $media->id,
            'user_id' => $userId,
        ]);
        if (! $userMedia->wasRecentlyCreated) {
            $userMedia->pushed_at = now();
            $userMedia->save();
        }

        $row = MediaMeta::where('media_id', $media->id)->first();
        if (! $row) {
            $row = new MediaMeta;
            $row->title = $media->title;
            $row->thumbnail = $media->thumbnail;
            $row->media_id = $media->id;
        }
        if (! $row->artist_id) {
            $row->artist_id = Artist::findOrCreate($trackArtist)->id;
        }
        $row->spotify_id = $trackId;
        $row->save();

        if ($importRecord) {
            $this->spotifyRepo->setImported($importRecord, $media->id);
        }
        $this->lc->info('imported media item '.$media->id);

        return true;
    }
}
