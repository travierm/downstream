<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class UserType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a users type by email';

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
      $this->info($user->email . "[$user->type] is on file for given hash.");
      if ($this->confirm('Is this the correct user?')) {
        $type = $this->choice('New user type?', ['basic', 'admin']);
        $user->type = $type;
        $user->save();
        $this->info("Updated user type to $type!");
      }else{
        return;
      }
    }
}
