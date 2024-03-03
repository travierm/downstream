<?php

namespace App\Console\Commands;

use App\Mail\JoinDateAnniversary;
use App\Models\User;
use DB;
use Illuminate\Console\Command;
use Mail;

class SendAnniversaryEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:anniversary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails to any users who joined the site on this day a year or more ago';

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
        $user = User::find(1);

        $currentDay = date('d');
        $currentMonth = date('m');
        $lastYear = date('Y') - 1;
        $todayLastYear = date('Y-m-d', strtotime('-1 year'));

        $users = User::where(DB::raw('DAY(created_at)'), $currentDay)
            ->where(DB::raw('MONTH(created_at)'), $currentMonth)
            ->where(DB::raw('YEAR(created_at)'), '<=', $lastYear)
            ->get();

        if ($users) {
            $this->info('Sending out '.count($users).' anniversary emails for today '.date('Y-m-d'));
        } else {
            $this->info('No user anniversary emails for today '.date('Y-m-d'));
            exit;
        }

        foreach ($users as $user) {
            $this->info('Sending anniversary email for '.$user->email);

            Mail::to($user)->send(new JoinDateAnniversary($user));
        }

        //Mail::to($user)->send(new JoinDateAnniversary($user));
    }
}
