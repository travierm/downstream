<?php

namespace App\Loggers;

use Illuminate\Log\Logger;

abstract class BaseLogger
{
    public Logger $logger;

    public function info(string $message, array $context = [])
    {
        $this->logger->info($message, $context);
    }

    public function error(string $message, array $context = [])
    {
        $this->logger->error($message, $context);
    }
}
