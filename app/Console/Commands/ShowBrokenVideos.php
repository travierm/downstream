<?php

namespace App\Console\Commands;

use App\Media;
use App\Media\YouTubeV2;
use Illuminate\Console\Command;

class ShowBrokenVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:broken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows broken videos';

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
        }

        $videos = Media::select(['meta', 'id', 'index'])->whereIn('index', $ids)->get();

        $formatted = [];
        foreach ($videos as $video) {
            $formatted[] = [
                'title' => $video->getMeta()->title,
                'id' => $video->id,
                'vid' => $video->index,
            ];
        }
        $this->table(['Title', 'Media ID', 'Video ID'], $formatted);
    }
}
