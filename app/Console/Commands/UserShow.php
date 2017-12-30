<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class UserShow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display info on user by hash';

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
      $hash = $this->ask('User hash?');
      $user = User::where('hash', $hash)->first();
      $this->info($user);
    }
}
