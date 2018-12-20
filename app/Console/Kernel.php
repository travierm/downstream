<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\UserShow;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('spotify:recommendations')
           ->everyFiveMinutes();
           //->appendOutputTo(storage_path('logs/recommendation-log'))
           //->emailOutputTo('moorlagrx@gmail.com');
        
        $schedule->command('globalq:process')
            ->everyMinute();

        $schedule->command('cron:anniversary')
            ->dailyAt('08:00')
            ->appendOutputTo(storage_path('logs/anniversary'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
