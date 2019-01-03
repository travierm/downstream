<?php

namespace App\Console\Commands;

use App\UserMedia;
use App\MediaMeta;
use App\UserSpotifyToken;
use App\Services\SpotifyAPI;
use Illuminate\Console\Command;

class spotifyImportClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:import-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean users DS Import of items that already exists in collection';

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
        $usersWithTokens = UserSpotifyToken::all();

        foreach($usersWithTokens as $token) {
            $api = SpotifyAPI::getInstanceWithToken($token);
    
            $spotifyUser = $api->me();

            $playlists = $api->getUserPlaylists($spotifyUser->id, [
                'limit' => 50
            ]);
            
            //find playlist named "DS Import"
            $importPlaylist = false;
            foreach($playlists->items as $playlist) {
                if(trim($playlist->name) == "DS Import") {
                    $this->info("Found DS_Import playlist");
                    $importPlaylist = $playlist;
                }
            }

            if(!$importPlaylist) {
                $this->info("Could not find DS Import playlist for user:" . $token->user_id);
                continue;
            }

            $track = $api->getPlaylistTracks($importPlaylist->id);

            if(!$track) {
                $this->info("No tracks to clean");
                continue;
            }
    

            $this->info("Cleaning " . count($track->items) . " tracks");

            $tracksForTrash = $this->cleanTracks($token->user_id, $track->items);

            $tracks = [
                'tracks' => $tracksForTrash
            ];
            

            $this->info("Deleting " . count($tracksForTrash) . " from users playlist");
            $api->deletePlaylistTracks($importPlaylist->id, $tracks);
        }
    }

    public function cleanTracks($userId, $tracks) 
    { 
        $trackTrashcan = [];
        foreach($tracks as $track) {
            $trackId = $track->track->id;
            $trackName = $track->track->name;
            $trackArtist = $track->track->artists[0]->name;
            $trackSearchQuery = $trackArtist . " " . $trackName;
            $this->info("Checking $trackSearchQuery against the meta table");

            $mediaMeta = MediaMeta::where('spotify_id', $trackId)->first();
            if(!$mediaMeta) {
                $this->info("Not a media item");
                continue;
            }

            $userMedia = UserMedia::where('media_id', $mediaMeta->media_id)
                ->where('user_id', $userId)
                ->first();


            //track exists in media and in users collection
            if($mediaMeta && $userMedia) {
                $this->info("Removing $trackSearchQuery from playlist");
                $trackTrashcan[] = ['id' => $trackId];
            }
        }

        return $trackTrashcan;
    }
}
