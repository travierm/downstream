<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class AnniversaryManual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anniversary:manual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Anniversary email manual by user_id';

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
        $userIds = $this->ask("User ID's to use?");

        $users = User::whereIn('id', explode(",", $userIds))
            ->get();

        if(!$users) {
            $this->error("No users found!");
            exit;
        }
        
        $headers = array_keys($users->toArray()[0]);
        $this->table($headers, $users->toArray());

        $confirmed = $this->confirm("Confirm to send Anniversary emails to these users");
        if(!$confirmed) {
            $this->info("stopped");
            exit;
        }

        $this->info("Sending out " . count($users) . " anniversary emails for today " . date("Y-m-d"));

        foreach($users as $user) {
            $this->info("Sending anniversary email for " . $user->email);

            Mail::to($user)->send(new JoinDateAnniversary($user));
        }
    }
}
