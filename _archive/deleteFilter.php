<?php

namespace App\Console\Commands;

use Cache;
use Illuminate\Console\Command;

class deleteFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filter:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $newFilters = [];
        $filters = Cache::get('filters.title');
        $filterValue = $this->ask('Filter value to remove?:', '');
        $found = false;

        if ($filterValue) {
            foreach ($filters as $filter) {
                $check = trim(strtolower($filterValue));

                if ($check !== $filter) {
                    $newFilters[] = $filter;
                } else {
                    $found = true;
                }
            }
        }

        if ($found) {
            $this->info("Deleted: $filterValue");
        } else {
            $this->error("Could not delete filter: $filterValue");
        }

        Cache::forever('filters.title', $newFilters);
    }
}
