<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class CompactorMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compactor:media {take}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates a media items media_meta table';

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
        $take = $this->argument('take');
        if(!$take) {
            $take = 10;
        }

        $ids = DB::table('media_meta');

        $this->info("Compacting $take media items");
    }
}
