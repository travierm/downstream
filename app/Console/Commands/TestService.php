<?php

namespace App\Console\Commands;

use App\Services\Discovery\LastFM;
use App\Services\Sources\SpotifyTrack;
use Illuminate\Console\Command;

class TestService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Developer testing for services';

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
     * @return int
     */
    public function handle()
    {
        $this->info('TEST');

        $results = LastFM::trackSearch('Panu - Solid Gold');
        //$results = SpotifyTrack::findIdByTitle('SOLID GOLD');

        //dd($results);
    }
}
