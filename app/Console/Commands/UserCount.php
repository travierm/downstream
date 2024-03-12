<?php

namespace App\Console\Commands;

use App\Models\User;
use DB;
use Illuminate\Console\Command;

class UserCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Total user count';

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
        $this->info('Total Users '.User::count());
        $this->info('Joined today '.User::whereDate('created_at', DB::raw('NOW()'))->count());
    }
}
