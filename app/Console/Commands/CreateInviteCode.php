<?php

namespace App\Console\Commands;

use App\Models\UserInviteCode;
use Illuminate\Console\Command;

class CreateInviteCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invite:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new invite code';

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
     * @return int
     */
    public function handle()
    {
        $stats = UserInviteCode::getInviteCodeStats();

        $this->info('Active Codes: '.$stats['activeCodes']);
        $this->info('Used Codes: '.$stats['usedCodes']);

        $note = $this->ask('Do you have any notes about this invite?');

        if ($this->confirm('Are you sure you want to create an invite code?')) {
            $invite = UserInviteCode::createInvite(null, $note);

            $this->info('Invite Code: '.$invite->invite_code);
            $this->info('Link: '.env('APP_LINK_URL').'/register?invite='.$invite->invite_code);
        }
    }
}
