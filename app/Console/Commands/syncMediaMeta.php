<?php

namespace App\Console\Commands;


use DB;
use App\Media;
use App\MediaMeta;
use App\Album;
use App\Artist;
use App\MediaTag;
use App\Tag;

use Illuminate\Console\Command;

class syncMediaMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:syncMeta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transition json meta to proper meta tables';

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
        $this->info("starting sync...");

        $items = Media::whereNotIn('id', function($query) {
            $query->select("media_id")
                ->from("media_meta");
        })->get();

        $this->info("syncing " . count($items) . " items with new meta tables");

        foreach($items as $media) {
            $meta = $media->getMeta();

            //save new meta row
            $mediaMeta = new MediaMeta;
            $mediaMeta->media_id = $media->id;
            $mediaMeta->title = $meta->title;
            $mediaMeta->thumbnail = $meta->thumbnail;
            $didSave = $mediaMeta->save();

            if(!$didSave) {
                $this->error("Could not create meta for " . $meta->title);
                continue;
            }else{
                $this->info("synced meta for " . $meta->title);
            }

            //create tags
            if(@$meta->tags) {
                $this->info("adding " . count($meta->tags) . " tags");
                foreach($meta->tags as $tagName) {
                    $tag = Tag::firstOrCreate($tagName);

                    //create meta => tag reference

                    $reference = MediaTag::firstOrCreate($tag->id, $media->id);
                }
            }
           
        }
    }
}
