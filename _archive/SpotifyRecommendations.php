<?php

namespace App\Console\Commands;

use App\Media;
use App\Media\YouTubeV2;
use App\MediaRemoteReference;
use App\Services\SpotifyAPI;
use Cache;
use DB;
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
        //filter media titles before doing matching
        $this->call('filter:runner');

        $take = 10;
        $api = SpotifyAPI::getInstance();
        $badMediaIds = Cache::get('spotifyFailedSearchMediaIds', []);
        $this->info('Found '.count($badMediaIds).' bad media ids');

        //get non references items
        $items = DB::table('media')
            ->select('*')
            ->whereNotIn('id', function ($query) {
                $query->select('media_id')->from('media_remote_references');
            })
            ->whereNotIn('id', $badMediaIds)
            ->orderBy('id', 'ASC')
            ->limit($take)
            ->get();

        if (count($items) <= 0) {
            $this->info('No tracks need recommendations');

            return true;
        }

        foreach ($items as $item) {
            //loop media items
            $media = Media::find($item->id);
            $this->info($media->getMeta()->title);

            $title = $media->getMeta()->title;
            $spotifyTrackId = @$media->getMeta()->spotify_id;

            if (! $spotifyTrackId) {
                //media doesn't have hard set spotify id so do search based on media title
                $response = $api->search($title, 'track', [
                    'limit' => 1,
                ]);

                if (@! $response->tracks->items[0]) {
                    $this->error('No spotify ref track found');
                    $badMediaIds[] = $media->id;

                    continue;
                }

                $spotifyTrackId = $response->tracks->items[0]->id;
            }

            $results = $api->getRecommendations([
                'limit' => 8,
                'seed_tracks' => [$spotifyTrackId],
            ]);

            if ($results->tracks) {
                //got seed recommendations
                foreach ($results->tracks as $track) {
                    //search youtube for track
                    $trackName = $track->artists[0]->name.' '.$track->name;
                    $youtubeResult = YouTubeV2::searchFirst($trackName);

                    if (! @$youtubeResult->vid) {
                        continue;
                    }

                    $ref = new MediaRemoteReference();
                    $ref->media_id = $item->id;
                    $ref->source = 'spotify';
                    $ref->source_id = $track->id;
                    $ref->index = $youtubeResult->vid;
                    $ref->title = $youtubeResult->info->title;
                    $ref->thumbnail = $youtubeResult->info->thumbnail;
                    $success = $ref->save();

                    if ($success) {
                        $this->info('discovered '.$ref->title);
                    }
                }
            } else {
                $badMediaIds[] = $media->id;
                $this->error('no seed recommendations');
            }
        }

        //cleanup
        $expiresAt = now()->addDays(3);
        Cache::put('spotifyFailedSearchMediaIds', $badMediaIds, $expiresAt);

        $count = DB::table('media')
            ->whereNotIn('id', function ($query) {
                $query->select('media_id')->from('media_remote_references');
            })
            ->whereNotIn('id', $badMediaIds)
            ->orderBy('id', 'ASC')
            ->limit($take)
            ->count();

        $this->info('done. '.$count.' more media items to process');
    }
}
