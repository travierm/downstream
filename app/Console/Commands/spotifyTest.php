<?php

namespace App\Console\Commands;

use DB;
use App\Artist;
use App\Media;
use App\MediaMeta;
use App\Media\YouTubeV2;
use App\UserMedia;
use App\Services\SpotifyAPI;
use App\UserSpotifyToken;
use Illuminate\Console\Command;

class spotifyTest extends Command
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

            //could not find existing playlist, create new
            if(!$syncList) {
                $api->createPlaylist([
                    'name' => 'DS Import'
                ]);

                foreach($playlists->items as $playlist) {
                    if(trim($playlist->name) == "DS Import") {
                        $this->info("Found DS_Import playlist");
                        $syncList = $playlist;
                    }
                }

                if(!$syncList) {
                    //could not find playlist after creatinf
                    exit;
                }

                $this->info("Created DS Import playlist for $userId");
            }

            $tracks = $api->getPlaylistTracks($syncList->id);
            
            //sync tracks to user collection
            foreach($tracks->items as $track) {
                $this->syncTrack($token->user_id, $track);
            } 
        } 
    }

    public function syncTrack($userId, $track)
    {
        
        $trackId = $track->track->id;
        $trackName = $track->track->name;
        $trackArtist = $track->track->artists[0]->name;
        $trackSearchQuery = $trackArtist . " " . $trackName;

        $this->info("Attempting to sync $trackSearchQuery");

        //check if this spotify_track exists on Downstream
        $media = DB::table('media_meta')
            ->where('spotify_id', $trackId)
            ->first();
        
        //found existing media so add it to users collection
        if(@$media->media_id) {
            $this->info("Found sync item in media table. Added to user collection.");
            $userMedia = UserMedia::firstOrCreate([
                'media_id' => $media->media_id,
                'user_id' => $userId
              ]);

            return true;
        }

        //Find youtube video to create media from
        
        $video = YouTubeV2::searchFirst($trackSearchQuery);

        $videoId = $video->vid;
        if(!@$video->vid) {
            //could not find related videos
            $this->info("Failed to find matching youtube vieo");
            return false;
        }

        $video = YouTubeV2::getInfo($video->vid);

        $meta = [];
        $meta['title'] = $video->snippet->title;
    
        //thumbnail
        if(@$video->snippet->thumbnails->standard->url) {
          $meta['thumbnail'] = @$video->snippet->thumbnails->standard->url;
        }else{
          $meta['thumbnail'] = @$video->snippet->thumbnails->high->url;
        }
    
        $meta['categoryId'] = $video->snippet->categoryId;
        if(@$video->snippet->tags) {
          $meta['tags'] = $video->snippet->tags;
        }

        $metaArray = $meta;
        $meta = json_encode($meta);
    
        $media = Media::firstOrCreate([
          'origin' => 'spotify#import',
          'type' => 'youtube',
          'subtype' => 'video',
          'index' => $videoId,
          'user_id' => $userId,
          'meta' => $meta
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

            $meta = $metaArray;
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
