<?php

namespace App\Console\Commands;

use Artisan;
use DB;
use App\Artist;
use App\Media;
use App\MediaMeta;
use App\Media\YouTube;
use App\Media\YouTubeV2;
use App\UserMedia;
use App\Services\SpotifyAPI;
use App\UserSpotifyToken;
use Illuminate\Console\Command;

class spotifyImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Spotify User Commands';

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
            
            //get list of user playlists
            $playlists = $api->getUserPlaylists($spotifyUser->id, [
                'limit' => 50
            ]);
            

            //find playlist named "DS Import"
            $syncList = false;
            foreach($playlists->items as $playlist) {
                if(trim($playlist->name) == "DS Import") {
                    $this->info("Found DS_Import playlist");
                    $syncList = $playlist;
                }
            }

            //could not find existing playlist, create new and exit
            if(!$syncList) {
                $result = $api->createPlaylist([
                    'name' => 'DS Import'
                ]);

                $this->info("Created DS Import playlist for user:$userId");
                
                exit();
            }

            $this->updatePlaylistImage($api, $syncList);

            $tracks = $api->getPlaylistTracks($syncList->id);

            if(count($tracks->items) <= 0) {
                $this->info("No items in users DS Import playlist");
                continue;
            }else{
                $this->info("Syncing " . count($tracks->items) . " tracks from playlist for user:$token->user_id");
            }
            
            //sync tracks to user collection
            foreach($tracks->items as $track) {
                $this->syncTrack($token->user_id, $track);
            } 
        } 

        $this->info("-----------------------");
        $this->info("Running import cleaner");
        $this->info("-----------------------");
        
        $this->call("spotify:import-clean");
    }

    public function updatePlaylistImage($api, $playlist)
    {
        $spotifyUser = $api->me();
        $spotifyUserId = $spotifyUser->id;

        if(!$playlist->images) {
            $imageData = base64_encode(file_get_contents(public_path("android-chrome-192x192.png")));

            try {
                $success = $api->updatePlaylistImage($playlist->uri, $imageData);
                dd($success);
            } catch (Exception $e) {
                echo 'Spotify API Error: ' . $e->getCode(); // Will be 404
            }

            $this->info("Updated user playlist image");
        }else{
            dd($playlist);
        }
    }

    public function syncTrack($userId, $track)
    {
        
        $trackId = $track->track->id;
        $trackName = $track->track->name;
        $trackArtist = $track->track->artists[0]->name;
        $trackSearchQuery = $trackArtist . " " . $trackName;

        $this->info("Attempting to import:" . $trackSearchQuery);

        //check if this spotify_track exists on Downstream
        $media = DB::table('media_meta')
            ->where('spotify_id', $trackId)
            ->first();
        
        //found existing media so add it to users collection
        if(@$media->media_id) {
            $this->info("Import already exists in media table.");

            $userMedia = UserMedia::firstOrCreate([
                'media_id' => $media->media_id,
                'user_id' => $userId
              ]);

            return true;
        }

        //Find youtube video to create media from
        $api = new YouTube();
        $results = $api->search($trackSearchQuery);
        if(!$results) {
            $this->error("Failed to find video using search query $trackSearchQuery");
            return false;
        }

        //get first video from youtube search query
        $video = $results[0];

        $meta = [
            "title" => $video->title,
            "thumbnail" => $video->thumbnail,
            "categoryId" => $video->categoryId,
            "tags" => $video->tags
        ];
    
        $media = Media::firstOrCreate([
          'origin' => 'spotify#import',
          'type' => 'youtube',
          'subtype' => 'video',
          'index' => $video->id,
          'user_id' => $userId,
          'meta' => json_encode($meta)
        ]);
    
        $userMedia = UserMedia::firstOrCreate([
          'media_id' => $media->id,
          'user_id' => $userId
        ]);

        //do media_meta check
        $row = MediaMeta::where('media_id', $media->id)->first();
        if(!$row) {
            //create or find artist
            $artist = Artist::findOrCreate($trackArtist);

            $row = new MediaMeta();
            $row->title = $meta['title'];
            $row->artist_id = $artist->id;
            $row->thumbnail = $meta['thumbnail'];
            $row->media_id = $media->id;
            $row->spotify_id = $trackId;
            $row->save();


        }

        $this->info("Created new media item " . $media->id);
    }
}
