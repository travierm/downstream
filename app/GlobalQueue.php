<?php

namespace App;

use Carbon;
use App\Media;
use Illuminate\Database\Eloquent\Model;

class GlobalQueue extends Model
{
    protected $table = "global_queue";

    //number of items that can be active at once
    private static $maxActiveItems = 6;
    private static $maxItemAliveDays = 12;

    public static function mediaIsQueued($mediaId) 
    {
        return self::where('media_id', $mediaId)->exists();
    }

    public static function canPushItem(GlobalQueue $item)
    {   
        $existingItem = self::where('media_id', $item->media_id)->first();

        return ($existingItem ? false : true);
    }

    public static function getActiveMedia()
    {
        $ids = self::where('active', 1)->orderBy('active_at', 'ASC')->pluck('media_id');

        $mediaItems = Media::byType('youtube')
            ->whereIn('id', $ids)
            ->get();
        
        return $mediaItems;   
    }

    public static function getAllMedia()
    {
        $ids = self::orderBy('created_at', "DESC")->pluck('media_id');

        $mediaItems = Media::byType('youtube')
            ->whereIn('id', $ids)
            ->get();
        
        return $mediaItems;   
    }
}
