<?php

namespace App\Providers;

use Cumulati\Monolog\LogContext;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /** var Logger $monolog */
        $monolog = Log::getLogger();

        //$logger = new Logger('context');
        // $handler = new StreamHandler('php://stdout');
        // $formatter = new LineFormatter(Monolog\Formatter\JsonFormatter::class);

        // $handler->setFormatter($formatter);
        // $logger->pushHandler($handler);

        // $logger->info('hi');

        //$monolog->getHandler()->setFormatter($formatter);

        LogContext::setDefaultLogger($monolog);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
