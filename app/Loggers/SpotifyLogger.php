<?php
namespace App\Loggers;

use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

class SpotifyLogger extends BaseLogger
{
    public Logger $logger;

    public function __construct(array $context = [])
    {
        $this->logger = Log::withContext($context);
    }
};
