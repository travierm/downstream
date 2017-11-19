<?php

namespace App\Console\Commands;

use App\Media;
use App\UserMedia;
use Illuminate\Console\Command;

class clearMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Media and UserMedia';

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
      $this->info('Deleting ' . Media::all()->count() . ' Media tracks');
      Media::truncate();

      $this->info('Deleting ' . UserMedia::all()->count() . ' User Media Collectables');
      UserMedia::truncate();
    }
}
