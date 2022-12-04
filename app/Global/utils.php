<?php

use Cumulati\Monolog\LogContext;

function lc(array $context): LogContext
{
    return new LogContext($context);
}

function superdd(...$vars)
{
    if (!in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) && !headers_sent()) {
        header('Access-Control-Allow-Origin: *');
        header('HTTP/1.1 500 Internal Server Error');
    }

    foreach ($vars as $v) {
        var_dump($v);
    }

    exit(1);
}
