<?php

namespace App\Console\Commands;

use Cache;
use DB;
use App\Media;
use App\Media\YouTubeV2;
use App\MediaRemoteReference;
use App\Services\SpotifyAPI;
use Illuminate\Console\Command;

class SpotifyRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:recommendations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Spotify track recommendations for Media';

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
        $take = 10;
        $api = SpotifyAPI::getInstance();
        $badMediaIds = Cache::get('spotifyFailedSearchMediaIds', []);
        $this->info("Found " . count($badMediaIds) . " bad media ids");

        //get non references items
        $items = DB::table('media')
            ->select('*')
            ->whereNotIn('id', function($query) {
                $query->select('media_id')->from('media_remote_references');
            })
            ->whereNotIn('id', $badMediaIds)
            ->orderBy('id', 'ASC')
            ->limit($take)
            ->get();

        if(count($items) <= 0) {
            $this->info("No tracks need recommendations");
            return true;
        }
        
        foreach($items as $item) {
            $media = Media::find($item->id);
            $this->info($media->getMeta()->title);

            $title = $media->getMeta()->title;
            $response = $api->search($title, 'track', [
                'limit' => 1
            ]);

            if(@!$response->tracks->items[0]) {
                $this->error("No spotify ref track found");
                $badMediaIds[] = $media->id;
                continue;
            }

            $spotifyTrackId = $response->tracks->items[0]->id;
            $spotifyTrackName = $response->tracks->items[0]->name;
            //maybe do levenshtine to make sure track name matches media title
            if($response->tracks->items[0]->id) {
                //spotify has track
                //do seed work
                $results = $api->getRecommendations([
                    'limit' => 8,
                    'seed_tracks' => [$spotifyTrackId]
                ]);

                if($results) {
                    //got seed recommendations
                    foreach($results->tracks as $track) {
                        //search youtube for track
                        $trackName = $track->artists[0]->name . " " . $track->name;
                        $youtubeResult = YouTubeV2::searchFirst($trackName);

                        if(!@$youtubeResult->vid) {
                            continue;
                        }
                        
                        $ref = new MediaRemoteReference();
                        $ref->media_id = $item->id;
                        $ref->source = 'spotify';
                        $ref->index = $youtubeResult->vid;
                        $ref->title = $youtubeResult->info->title;
                        $ref->thumbnail = $youtubeResult->info->thumbnail;
                        $success = $ref->save();

                        if($success) {
                            $this->info("discovered " . $ref->title);
                        }
                    }
                }
            }
        }

        //cleanup
        $expiresAt = now()->addDays(3);
        Cache::put('spotifyFailedSearchMediaIds', $badMediaIds, $expiresAt);

        $count = DB::table('media')
            ->whereNotIn('id', function($query) {
                $query->select('media_id')->from('media_remote_references');
            })
            ->whereNotIn('id', $badMediaIds)
            ->orderBy('id', 'ASC')
            ->limit($take)
            ->count();
        
        $this->info("done. " . $count . " more media items to process");
    }
}
