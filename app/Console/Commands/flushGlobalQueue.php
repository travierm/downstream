<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\GlobalQueue;

class flushGlobalQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'globalq:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush all items and reset auto_inc on global_queue table';

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
        $count = count(GlobalQueue::all());

        $this->info("Flushing $count items from Global Queue");

        GlobalQueue::truncate();
    }
}
