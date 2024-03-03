<?php

namespace App\Console\Commands;

use App\Models\User;
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
    protected $description = 'Display info on user by id';

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
        $id = $this->ask('User ID?');
        $user = User::where('id', $id)->first();
        $this->info($user);
    }
}
