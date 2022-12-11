<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        global $setupAlreadyRan;
        /**
         * This disables the exception handling to display the stacktrace on the console
         * the same way as it shown on the browser
         */
        parent::setUp();
        $this->withoutExceptionHandling();

        if (! $setupAlreadyRan) {
            $this->runOnce();
        }

        $setupAlreadyRan = true;
    }

    private function runOnce()
    {
        if (config('db.database') !== 'ds_main') {
            Artisan::call('migrate:fresh');
        }
    }
}
