<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class AddTitleFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filter:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add filter values to table';

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
     * @return int
     */
    public function handle()
    {
        $totalFilters = DB::table('title_filters')->count();
        $this->info($totalFilters . ' active filters');

        $filterValue = $this->ask("What filter would you like to add?");

        $exists = DB::table('title_filters')->where(DB::raw('BINARY `value`'), $filterValue)->exists();
        if($exists) {
            $this->error('Filter already exists');
            return;
        }

        if($this->confirm("Add {$filterValue} filter?")) {
            DB::table('title_filters')->insert([
                'value' => trim($filterValue)
            ]);

            $this->call('filter:run');
        }
    }
}
