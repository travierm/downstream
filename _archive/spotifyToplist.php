<?php

namespace App\Console\Commands;

use App\Media\YouTubeV2;
use App\MediaTempItem;
use App\Services\SpotifyAPI;
use Illuminate\Console\Command;

class spotifyToplist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:toplist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search Spotify API';

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
        $api = SpotifyAPI::getInstance();
        if (! $api) {
            $this->error('Bad Spotify Client ID or Secret');

            return;
        }

        //get toplist
        $playlists = $api->getUserPlaylist('spotify', '37i9dQZF1DXcBWIGoYBM5M');
        $tracks = $playlists->tracks->items;

        //how many toplists tracks to pull each run
        $count = 0;
        $rounds = 30;
        foreach ($tracks as $track) {
            if ($count == $rounds) {
                $this->info('finished!');

                return;
            }

            $count++;

            $name = $track->track->name;
            $artist = $track->track->artists[0]->name;

            $trackId = $track->track->id;
            $trackString = "$artist - $name";
            $this->info($trackString);

            $resp = YouTubeV2::searchFirst($trackString);
            if ($resp) {
                $item = MediaTempItem::where('index', $resp->vid)->exists();
                if ($item) {
                    //already an item
                    $this->error('already imported');

                    continue;
                }

                $check = MediaTempItem::create([
                    'source' => 'spotify:toplist',
                    'source_id' => $trackId,
                    'title' => $resp->info->title,
                    'index' => $resp->vid,
                    'thumbnail' => $resp->info->thumbnail,
                ]);

                if ($check) {
                    $this->info('created');
                }
            }
        }
    }
}
