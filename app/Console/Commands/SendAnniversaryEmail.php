<?php

namespace App\Console\Commands;

use DB;
use Mail;
use App\User;
use App\Mail\JoinDateAnniversary;
use Illuminate\Console\Command;

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

        $currentDay = Date("d");
        $currentMonth = Date("m");
        $lastYear = Date("Y") - 1;
        $todayLastYear = date("Y-m-d", strtotime("-1 year"));

        $users = User::where(DB::raw("DAY(created_at)"), $currentDay)
            ->where(DB::raw("MONTH(created_at)"), $currentMonth)
            ->where(DB::raw("YEAR(created_at)"), "<=", $lastYear)
            ->get();
        
        foreach($users as $user) {
            Mail::to($user)->send(new JoinDateAnniversary($user));
        }

        //Mail::to($user)->send(new JoinDateAnniversary($user));
    }
}
