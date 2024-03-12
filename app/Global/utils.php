<?php

use Cumulati\Monolog\LogContext;
use App\Global\RequestLogContext;

function getRequestLogContext(): LogContext
{
    return RequestLogContext::getContext();
}

/**
 * Get the Request Log Context
 *
 * Allows log messages to be chained using a request ID
 *
 * @return LogContext
 */
function rlc(): LogContext
{
    return getRequestLogContext();
}

function lc(array $context = []): LogContext
{
    return new LogContext($context);
}

function superdd(...$vars)
{
    if (! in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) && ! headers_sent()) {
        header('Access-Control-Allow-Origin: *');
        header('HTTP/1.1 500 Internal Server Error');
    }

    foreach ($vars as $v) {
        var_dump($v);
    }

    exit(1);
}

/**
 * Dump the passed variables and exit the script but also call toArray() on any object it can and on arrays of objects passed in.
 *
 * @param  mixed  $args
 * @return void
 */
function dda(...$args): void
{
    $args = array_map(function ($arg) {
        if (is_object($arg) && method_exists($arg, 'toArray')) {
            return $arg->toArray();
        }

        if (is_array($arg)) {
            return array_map(function ($item) {
                return is_object($item) && method_exists($item, 'toArray')
                    ? $item->toArray()
                    : $item;
            }, $arg);
        }

        return $arg;
    }, $args);

    dd(...$args);
}
