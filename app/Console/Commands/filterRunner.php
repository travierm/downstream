<?php

namespace App\Console\Commands;

use Cache;
use App\Media;
use Illuminate\Console\Command;

class filterRunner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filter:runner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs filters against media data';

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
        //Title Runner
        $this->info("Booting title filter runner...");
        $filters = Cache::get('filters.title', []);

        if(!$filters) {
            $this->error("No filters found");
            return;
        }

        $this->info("Found " . count($filters) . " filter definitions");
        $media = Media::all();

        foreach($media as $item) {
            $title = $item->getMeta()->title;
            $filteredTitle = $this->applyFilters($title, $filters);

            if($title !== $filteredTitle) {
                $meta = $item->getMeta();
                $meta->title = trim($filteredTitle);
                $item->meta = json_encode($meta);
                $good = $item->save();

                if($good) {
                    $this->info("updated $title => $filteredTitle");
                }
            }
        }
    }

    private function applyFilters($value, $filters)
    {
        foreach($filters as $filter) {
            $value = str_replace($filter, "", $value);
        }

        return $value;
    }
}
