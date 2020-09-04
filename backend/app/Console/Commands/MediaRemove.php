<?php

namespace App\Console\Commands;

use App\Media;
use Illuminate\Console\Command;

class MediaRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:remove {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove media from media table and user_media';

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
        //
    }
}
