<?php

namespace App\Global;

use Cumulati\Monolog\LogContext;

class RequestLogContext
{
    private static LogContext $logContext;

    public static function getContext(): LogContext
    {
        return self::$logContext;
    }

    public static function startRequestLog(array $context = [])
    {
        self::$logContext = new LogContext($context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::$logContext->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::$logContext->error($message, $context);
    }
}
