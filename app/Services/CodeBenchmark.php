<?php

namespace App\Services;

class CodeBenchmark
{
    private $startTime;

    public function start(): void
    {
        $this->startTime = microtime(true);
    }

    public function end(): string
    {
        if(!$this->startTime) {
            throw new \Exception("Timer start method has not been called.");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $this->startTime;

        // Format to the nearest microsecond
        $executionTime = number_format($executionTime, 6);

        // Reset start time
        $this->startTime = null;

        return $executionTime;
    }
}
