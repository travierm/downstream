<?php

namespace App\Console\Commands;

use App\Media;
use App\Media\YouTubeV2;
use Illuminate\Console\Command;

class AutofixVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yt:autofix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attempts to autofix broken videos';

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
        $ids = YouTubeV2::getBrokenVideoIds();

        if (! $ids) {
            $this->info('No broken videos!');

            return;
        }

        $videos = Media::select(['meta', 'id', 'index'])->whereIn('index', $ids)->get();
        if (count($videos) <= 0) {
            $this->error('No media could be found for broken video ids!');

            return;
        }

        $formatted = [];
        foreach ($videos as $video) {
            $formatted[] = [
                'title' => $video->getMeta()->title,
                'id' => $video->id,
                'vid' => $video->index,
            ];
        }

        $this->info('Broken Videos:');
        $this->table(['Title', 'Media ID', 'Video ID'], $formatted);
        $continue = $this->confirm('Attempt Autofix?');

        if (! $continue) {
            return;
        }

        $results = [];
        foreach ($formatted as $video) {
            $result = YouTubeV2::searchFirst($video['title']);

            $info = YouTubeV2::getInfo($result->vid);

            if ($info) {
                //found replacement video
                $this->info('Found Replacement!');
                $this->error('Broken: '.$video['title']);
                $this->info('Autofix: '.$info->snippet->title);

                if ($this->confirm('Update media for this broken video?')) {
                    $result = YouTubeV2::updateMedia($video['id'], $result->vid);
                    if ($result) {
                        $this->info('successfully autofixed');
                    } else {
                        $this->error('error with trying to autofix');
                    }
                }
            }
        }
    }
}
