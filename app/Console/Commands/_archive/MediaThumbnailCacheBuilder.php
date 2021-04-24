<?php

namespace App\Console\Commands;

use App\Media;
use App\MediaMeta;
use ColorThief\ColorThief;
use Illuminate\Console\Command;

class MediaThumbnailCacheBuilder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:thumbnail_cache_builder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set thumbnail colors for media items meta';

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
        $limit = 50;

        $media = Media::join('media_meta', 'media_meta.media_id', '=', 'media.id')
            ->whereNull('media_meta.thumbnail_colors')
            ->orderBy('media.created_at', 'DESC')
            ->limit(50)
            ->get();

        foreach($media as $item) {

            $sourceImage = $item->getMeta()->thumbnail;
            $palette = ColorThief::getPalette($sourceImage, 4, 1);

            if(!$palette) {
                $this->error("Could not fetch color for " . $item->getMeta()->title);
                continue;
            }

            $colors = [];
            foreach($palette as $color) {
                $hex = $this->getColorHex($color[0], $color[1], $color[2]);

                $colors[] = $hex;
            }

            $colorString = implode(', ', $colors);

            $meta = MediaMeta::where('media_id', $item->id)->first();

            if($meta) {
                $meta->thumbnail_colors = $colorString;
                $meta->save();
            }
        }

        $remaining = Media::join('media_meta', 'media_meta.media_id', '=', 'media.id')
            ->whereNull('media_meta.thumbnail_colors')
            ->orderBy('media.created_at', 'DESC')
            ->count();
        
        $this->info($remaining . ' items left to fetch thumbnail colors for');
    }

    private function getColorHex($r, $g, $b)
    {
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}
