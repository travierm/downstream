<?php

namespace App\Console\Commands;

use App\Models\MediaMeta;
use App\Services\SpotifyAPI;
use App\Models\UserMedia;
use App\Models\UserSpotifyToken;
use Illuminate\Console\Command;

class SyncCollectionWithSpotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:collection-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync User collections with Spotify DS Collection Playlist';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userIds = [38, 1];

        foreach ($userIds as $userId) {
            $token = UserSpotifyToken::where('user_id', $userId)->first();

            if ($token) {
                $this->processUserCollection($userId, $token);
            }
        }
    }

    private function updatePlaylistImage($api, $playlist)
    {
        $spotifyUser = $api->me();
        $spotifyUserId = $spotifyUser->id;
        $imageData = base64_encode(file_get_contents(public_path('android-chrome-192x192.jpg')));

        $success = $api->updatePlaylistImage($playlist->id, $imageData);

        return;

        if (! $playlist->images) {
            $imageData = base64_encode(file_get_contents(public_path('android-chrome-192x192.png')));
            try {
                $success = $api->updatePlaylistImage($playlist->id, $imageData);
            } catch (Exception $e) {
                echo 'Spotify API Error: '.$e->getCode(); // Will be 404
            }

            $this->info('Updated user playlist image');
        } else {
            dd($playlist);
        }
    }

    private function processUserCollection($userId, UserSpotifyToken $token)
    {
        $api = SpotifyAPI::getInstanceWithToken($token);

        $spotifyUser = $api->me();

        //get list of user playlists
        $playlists = $api->getUserPlaylists($spotifyUser->id, [
            'limit' => 50,
        ]);

        //find playlist named "DS Collection"
        $syncList = false;
        foreach ($playlists->items as $playlist) {
            if (trim($playlist->name) == 'DS Collection') {
                $this->info('Found DS_Import playlist');
                $syncList = $playlist;
            }
        }

        //could not find existing playlist, create new and exit
        if (! $syncList) {
            $result = $api->createPlaylist([
                'name' => 'DS Collection',
                'description' => 'Playlist filled with Spotify Tracks found in your Downstream Collection',
            ]);

            $this->info("Created DS Import playlist for user:$userId");

            return true;
        }

        $userMediaIds = UserMedia::where('user_id', $userId)->pluck('media_id');
        $spotifyTrackIds = MediaMeta::whereIn('media_id', $userMediaIds)->pluck('spotify_id');

        $trackIds = array_values($spotifyTrackIds->all());

        foreach ($trackIds as $track) {
            try {
                $api->addPlaylistTracks($syncList->id, [
                    $track,
                ]);
            } catch(Exception $e) {
            }
        }

        $this->info('Added '.count($spotifyTrackIds)." tracks to User{$userId} collection playlist");
        //dd($spotifyTrackIds);

        //check what user collection tracks can be sync to Spotify
        //check what possible tracks are not in users playlist
        //add missing tracks
        //remove missing tracks
    }
}
