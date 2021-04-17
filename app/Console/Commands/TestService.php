<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Discovery\LastFM;

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
        LastFM::similarTracks('pnau', 'solid gold');
    }
}
