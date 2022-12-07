<?php

namespace App\Services\Spotify;

use DB;
use App\Models\Media;
use App\Models\Artist;
use App\Models\MediaMeta;
use App\Models\UserMedia;
use App\Repos\SpotifyRepo;
use App\MediaType\YouTubeV2;
use App\Services\SpotifyAPI;
use App\Loggers\SpotifyLogger;
use App\Models\UserSpotifyToken;
use App\Services\YoutubeService;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifySyncService
{
    private SpotifyLogger $lc;

    public function __construct(public SpotifyRepo $spotifyRepo)
    {
        $this->lc = new SpotifyLogger([
            'group' => 'spotify@import'
        ]);
    }

    public function syncByToken(UserSpotifyToken $token): void
    {
        try {
            $spotifyApi = SpotifyAPI::getInstanceWithToken($token);
        } catch (\Exception $e) {
            throw $e;
        }

        $importPlaylist = $this->getOrCreateImportPlaylist($spotifyApi, $spotifyApi->me());
        if (!$importPlaylist) {
            return;
        }

        $tracks = $spotifyApi->getPlaylistTracks($importPlaylist->id);
        $trackCount = count($tracks->items);

        $this->lc->info(
            sprintf('found %s tracks in import playlist', $trackCount)
        );

        foreach ($tracks->items as $track) {
            if (!$track->track) {
                continue;
            }

            $successful = $this->syncTrack($token->user_id, $track);

            if ($successful) {
                $this->lc->info('imported spotify track');
            }
        }
    }


    public function getOrCreateImportPlaylist(SpotifyWebAPI $spotifyApi, object $spotifyUser): ?object
    {
        //get list of user playlists
        $playlists = $spotifyApi->getUserPlaylists($spotifyUser->id, [
            'limit' => 50
        ]);


        //find playlist named "DS Import"
        $importList = false;
        foreach ($playlists->items as $playlist) {
            if (trim($playlist->name) == "DS Import") {
                $importList = $playlist;
            }
        }

        //could not find existing playlist, create new and exit
        if (!$importList) {
            $spotifyApi->createPlaylist([
                'name' => 'DS Import'
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

        if (!$playlist->images) {
            $imageData = base64_encode(file_get_contents(public_path("android-chrome-192x192.png")));

            try {
                $success = $api->updatePlaylistImage($playlist->uri, $imageData);
                dd($success);
            } catch (\Exception $e) {
                echo 'Spotify API Error: ' . $e->getCode(); // Will be 404
            }

            $this->lc->info("updated user playlist image");
        } else {
            dd($playlist);
        }
    }

    public function syncTrack(int $userId, object $track): bool
    {
        $trackId = $track->track->id;
        $trackName = $track->track->name;
        $trackArtist = $track->track->artists[0]->name;
        $trackArtistId = $track->track->artists[0]->id;
        $trackSearchQuery = $trackArtist . " " . $trackName;

        $this->lc->info("attempting to import with query " . $trackSearchQuery);
        if (!$trackId) {
            $this->lc->error('spotify track id is null');
        }

        //check if this spotify_track exists on Downstream
        $media = DB::table('media_meta')
            ->where('spotify_id', $trackId)
            ->first();

        //found existing media so add it to users collection
        if (@$media->media_id) {
            $this->lc->info("import already exists in media table");

            $userMediaData = [
                'media_id' => $media->media_id,
                'user_id' => $userId
            ];

            $userMedia = UserMedia::where($userMediaData)->first();

            if ($userMedia) {
                $this->lc->info('pushing item since it already existed in users collection');
                $userMedia->pushed_at = now();
                $userMedia->save();
            } else {
                $this->lc->info('adding item to users collection');
                UserMedia::create($userMediaData);
            }

            return true;
        }

        $importRecord = $this->spotifyRepo->createSpotifyImport($userId, $trackName, $trackArtist, $trackSearchQuery);
        //Find youtube video to create media from
        $videos = YoutubeService::searchByQuery($trackSearchQuery);
        if (!$videos && $videos[0]) {
            $this->lc->error("failed to find video using search query $trackSearchQuery");
            return false;
        }

        $video = $videos[0];
        $videoIndex = $video->videoId;

        $media = Media::where('index', $videoIndex)->first();

        if (!$media) {
            $media = Media::firstOrCreate([
                'origin' => 'spotify#import',
                'type' => 'youtube',
                'subtype' => 'video',
                'index' => $video->videoId,
                'user_id' => $userId,
                'title' => $video->title,
                'spotify_id' => $trackId,
                'thumbnail' => $video->thumbnail
              ]);
        }

        if (!$media->spotify_id || $media->spotify_id !== $trackId) {
            $media->spotify_id = $trackId;
            $media->save();
        }

        UserMedia::firstOrCreate([
          'media_id' => $media->id,
          'user_id' => $userId
        ]);

        //do media_meta check
        $row = MediaMeta::where('media_id', $media->id)->first();
        if (!$row) {
            //create or find artist
            $artist = Artist::findOrCreate($trackArtist);

            $row = new MediaMeta();
            $row->title = $video->title;
            $row->artist_id = $artist->id;
            $row->thumbnail = $video->thumbnail;
            $row->media_id = $media->id;
            $row->spotify_id = $trackId;
            $row->save();
        }

        $this->spotifyRepo->setImported($importRecord, $media->id);
        $this->lc->info("created new media item " . $media->id);

        return true;
    }
}
