<?php

namespace App\Console\Commands;

use App\Models\UserMedia;
use App\Models\UserMediaPlays;
use App\Services\CodeBenchmark;
use Illuminate\Console\Command;
use Cumulati\Monolog\LogContext;
use App\Services\DailyMixService;

class HyrdateDailyMixCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hydrate:daily-mix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hydrate the daily mix cache for active users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $benchmark = new CodeBenchmark();
        $benchmark->start();
        $lc = new LogContext();

        $activeUsers = UserMedia::whereDate('created_at', '>', now()->subDays(7))
            ->with('user')
            ->groupBy('user_id')
            ->get('user_id');

        $lc->info('found active users', ['count' => $activeUsers->count(), 'users' => $activeUsers]);

        $dailyMixService = new DailyMixService();
        foreach($activeUsers as $user) {
            $dailyMixService->generateDailyMix($user->user);
        }

        $lc->info('finished hydrating daily mix cache', ['exec_time' => $benchmark->end()]);

        return Command::SUCCESS;
    }
}
