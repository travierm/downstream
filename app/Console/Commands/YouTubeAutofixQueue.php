<?php

namespace App\Console\Commands;

use Cache;
use App\Media;
use YouTubeService;
use App\Media\YouTubeV2;
use Illuminate\Console\Command;

class YouTubeAutofixQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:autofix-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batches through media items looking for bad videos to autofix';

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
        $currentMediaId = 1;
        $lastMediaId = Cache::get('youtubeAutofix.lastMediaId');

        if(!Cache::get('youtubeAutofix.fixedMediaItems')) {
            Cache::set('youtubeAutofix.fixedMediaItems', 0);
        }

        if($lastMediaId) {
            $mostRecentMediaId = Media::orderBy('created_at', 'desc')->first()->id;

            // Check next media id if not on last item yet
            if($mostRecentMediaId !== $lastMediaId) {
                $currentMediaId = $lastMediaId + 1; 
            }
        }

        $currentMediaId = 67;

        $currentMedia = Media::find($currentMediaId);
        $mediaIsAvailable = YouTubeService::getVideoInfo($currentMedia->index);

        if(!$mediaIsAvailable) {
            $currentMediaTitle = $currentMedia->getMeta()->title;

            $response = YouTubeV2::searchFirst($currentMediaTitle);
            $updatedMediaInfo = YouTubeV2::getInfo($response->vid);

            if($updatedMediaInfo) {
                // Found replacement video

                $success = YouTubeV2::updateMedia($currentMedia->id, $response->vid);
                if($success) {
                    $this->info("Fixed broken media with id: " . $currentMedia->id);
                    $this->info($currentMedia->id . " updated title from [" . $currentMediaTitle . "] to [" . $updatedMediaInfo->snippet->title . "]");

                    Cache::increment('youtubeAutofix.fixedMediaItems');
                }else{
                    $this->error('Failed to autofix media with id ' . $currentMedia->id);
                }
            }
        }

        Cache::set('youtubeAutofix.lastMediaId', $currentMediaId);
    }
}
