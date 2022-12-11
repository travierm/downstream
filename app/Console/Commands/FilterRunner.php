<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\MediaMeta;
use App\Models\MediaTempItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FilterRunner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filter:run';

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
        $filters = DB::table('title_filters')->pluck('value');
        if (! $filters) {
            $this->error('No filters found');

            return;
        }

        $this->info('Found '.count($filters).' active filters');
        $media = Media::all();

        foreach ($media as $item) {
            $title = @$item->title;
            if (! $title) {
                continue;
            }

            $filteredTitle = trim($this->applyFilters($title, $filters));

            $mediaMeta = MediaMeta::find($item->id);

            if ($title !== $filteredTitle || ($mediaMeta && $mediaMeta->title !== $filteredTitle)) {
                $success = Media::where('id', $item->id)->update([
                    'title' => $filteredTitle,
                ]);

                $mediaMeta = MediaMeta::find($item->id);
                if ($mediaMeta) {
                    $mediaMeta->title = $filteredTitle;
                    $mediaMeta->save();
                }

                if ($success) {
                    $this->info("updated {$item->id} $title => $filteredTitle");
                }
            }
        }

        $this->info('doing temp items now...');
        $tempItems = MediaTempItem::all();
        foreach ($tempItems as $item) {
            $title = $item->title;
            $filteredTitle = $this->applyFilters($title, $filters);

            if ($title !== $filteredTitle) {
                $item->title = trim($filteredTitle);
                $item->save();
                $this->info("updated $title => $filteredTitle");
            }
        }
    }

    private function applyFilters($value, $filters)
    {
        foreach ($filters as $filter) {
            $value = str_replace($filter, '', $value);
        }

        return $value;
    }
}
