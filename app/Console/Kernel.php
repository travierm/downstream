<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\YouTubeAutofixQueue'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {   
        $schedule->command('filter:run')->hourly();

        // Continuously loop through media collection and autofix videos
        /*($schedule->command('youtube:autofix-queue')
           ->everyMinute()
           ->appendOutputTo(storage_path('logs/autofix.log'));*/

        // Discover new items for users based on media they already collected
        /*$schedule->command('spotify:recommendations')
           ->everyTenMinutes()
           ->appendOutputTo(storage_path('logs/discovery.log'));*/
        
        // Import new tracks found in a user's DS Import playlist on Spotify
        /*$schedule->command('spotify:import')
           ->everyMinute()
           ->appendOutputTo(storage_path('logs/spotify-import.log'));*/
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
