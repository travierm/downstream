<?php

namespace App\Global;

use Cumulati\Monolog\LogContext;

class RequestLogContext
{
    private static LogContext $logContext;
    private static string $requestId;

    public static function startRequestLog(array $context = [])
    {
        $data = array_merge($context, [
             'request_id' => self::$requestId,
        ]);

        self::$requestId = str_random(32);
        self::$logContext = new LogContext($data);
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
