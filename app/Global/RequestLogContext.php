<?php

namespace App\Global;

use Cumulati\Monolog\LogContext;

class RequestLogContext
{
    private LogContext $logContext;
    public readonly string $requestId;

    public static function startRequestLog(array $context = [])
    {
        self::$requestId = str_random(32);
        self::$logContext = new LogContext([
            'request_id' => self::$requestId,
            ...$context
        ]);
    }

    public static function info(string $message, array $context): void
    {
        self::$logContext->info($message, $context);
    }

    public static function error(string $message, array $context): void
    {
        self::$logContext->error($message, $context);
    }
}
