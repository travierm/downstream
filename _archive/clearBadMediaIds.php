<?php

namespace App\Console\Commands;

use Cache;
use Illuminate\Console\Command;

class clearBadMediaIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove bad media ids so they can be ran against spotify another time';

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
        $expiresAt = now()->addDays(3);
        Cache::put('spotifyFailedSearchMediaIds', [], $expiresAt);

        $this->info('cleared bad media ids from cache');
    }
}
