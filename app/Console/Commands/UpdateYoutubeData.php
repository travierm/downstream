<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use App\Services\YoutubeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UpdateYoutubeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:update-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update YouTube data to comply with API policies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentMediaId = 1;
        $lastMediaId = Cache::get('youtubeData.lastMediaId');

        if (! Cache::get('youtubeData.lastMediaId')) {
            Cache::set('youtubeData.lastMediaId', 0);
        }

        if ($lastMediaId) {
            $mostRecentMediaId = Media::orderBy('created_at', 'desc')->first()->id;

            if ($mostRecentMediaId !== $lastMediaId) {
                $currentMediaId = $lastMediaId + 1;
            } else {
                Log::info('reset lastMediaId for command');
                Cache::set('youtubeData.lastMediaId', $currentMediaId);
            }
        }

        Log::info('running youtube data update', [
            'startingMediaId' => $currentMediaId,
        ]);

        $items = Media::where('id', '>', $currentMediaId)
            ->limit(200)
            ->get();

        $titleFilters = DB::table('title_filters')->pluck('value');

        foreach ($items as $item) {
            $video = YoutubeService::getVideoById($item->index);

            if (! $video) {
                Log::info('soft deleting media item', [
                    'mediaId' => $item->id,
                    'title' => $item->title,
                    'thumbnail' => $item->thumbnail,
                ]);

                $item->delete();

                Cache::put('youtubeData.lastMediaId', $item->id);

                continue;
            }

            $videoTitle = filterTitle($video->title, $titleFilters->toArray());
            $itemTitle = filterTitle($item->title, $titleFilters->toArray());

            if ($itemTitle !== $videoTitle) {
                $item->title = $videoTitle;
            }

            if ($video->thumbnail !== $item->thumbnail) {
                $item->thumbnail = $video->thumbnail;
            }

            if ($item->isDirty()) {
                Log::info('updated media item', [
                    'mediaId' => $item->id,
                    'dirtyFields' => implode(', ', $item->getDirty()),
                ]);

                $item->save();
            }

            Cache::put('youtubeData.lastMediaId', $item->id);
        }
    }
}
