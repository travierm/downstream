<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user account';

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
        $displayName = $this->ask('display name?');
        $email = $this->ask('email?');
        $password = $this->ask('password?');

        $existing = User::where('email', $email)->first();
        if ($existing) {
            return $this->info('the user already exists');
        }

        if (!$this->confirm('do you want to create this account?')) {
            return;
        }

        User::create([
            'hash' => Str::random(40),
            'display_name' => $displayName,
            'password' => Hash::make($password),
            'email' => $email,
            'api_token' => Str::random(60),
        ]);
    }
}
