<?php

namespace App\Console\Commands;

use App\Media;
use App\Media\YouTubeV2;
use Illuminate\Console\Command;

class UpdateMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates media index and meta';

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
        $this->info("Lets update a media item!");
        $mediaId = $this->ask("Media ID?");

        $media = Media::find($mediaId);

        if(!$media) {
            $this->error("Could not find media by that ID");
            return;
        }
        $this->info("Found!");
        $this->comment($media->getMeta()->title);
        $videoId = $this->ask("New Video ID to use for Media?");

        $res = YouTubeV2::updateMedia($mediaId, $videoId);

        if(!$res) {
            $this->error("Failed to update media!");
            return;
        }

        $this->info("Updated media item!");
        $this->comment("title: " . $media->getMeta()->title);
        $this->comment('index: ' . $media->index);
    }
}
