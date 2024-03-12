<?php

namespace App\Console\Commands;

use App\Models\UserInviteCode;
use Illuminate\Console\Command;

class ClearInviteCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invite:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all used invite codes';

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
        $this->info('clearing unused invite codes...');

        UserInviteCode::whereNull('used_by')
            ->whereNull('used_at')
            ->where('created_at', '<', now()->subDays(7))
            ->delete();

        $this->info('cleared all unused invite codes.');
    }


}
