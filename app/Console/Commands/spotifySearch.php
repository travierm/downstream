<?php

namespace App\Console\Commands;

use App\Services\SpotifyAPI;
use Illuminate\Console\Command;

class spotifySearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:search';

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
        if(!$api) {
            $this->error("Bad Spotify Client ID or Secret");
            return;
        }
        
        $query = $this->ask("Spotify Track Query?", "");

        $response = $api->search($query, 'track', [
            'limit' => 1
        ]);

        dd($response);
    }
}
