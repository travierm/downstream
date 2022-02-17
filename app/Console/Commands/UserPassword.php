<?php

namespace App\Console\Commands;

use Hash;
use App\Models\User;
use Illuminate\Console\Command;

class UserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:password {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change users password by id';

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
        $userId = $this->argument('userId');


        $user = User::find($userId);
        if(!$user) {
            $this->error("Could not find user by id $userId");
            return false;
        }

        $this->info("Changing passsword for $user->display_name");
        $confirmedPassword = false;
        do {

            $password = $this->secret('New password?');
            $confirmPassword = $this->secret('Confirm new password?');

            if($confirmPassword === $password) {
                $this->info("Password matched!");
                $confirmedPassword = true;
            }else{
                $this->error("Passwords do not match!");
            }
        } while($confirmedPassword == false);

        if($this->confirm("Are you sure you want to change $user->display_name password?")) {
            $user->password = Hash::make($password);
            $user->save();

            $this->info("Password changed!");
        }else{
            $this->info("Doing nothing.");
        }
    }
}
