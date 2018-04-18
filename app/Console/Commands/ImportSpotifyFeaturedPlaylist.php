<?php

namespace App\Console\Commands;

use DB;
use App\Media;
use App\Services\SpotifyAPI;
use Illuminate\Console\Command;

class ImportSpotifyFeaturedPlaylist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:references';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Spotify APi';

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
        $take = 5;
        $api = SpotifyAPI::getInstance();

        //get non references items
        $items = DB::table('media')
            ->select('*')
            ->whereNotIn('id', function($query) {
                $query->select('media_id')->from('media_remote_references');
            })
            ->orderBy('id', 'ASC')
            ->limit($take)
            ->get();
        
        foreach($items as $item) {
            $media = Media::find($item->id);
            $this->info($media->getMeta()->title);

            $title = "LIL peep beamer boy";
            $response = $api->search($title, 'track', [
                'limit' => 1
            ]);

            dd($response);
            exit;
        }
    }
}
