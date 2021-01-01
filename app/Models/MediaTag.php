<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaTag extends Model
{
    public $timestamps = false;

    public static function firstOrCreate($tagId, $mediaId) 
    {
        $obj = static::where('tag_id', $tagId)
            ->where('media_id', $mediaId)
            ->first();
        
        if(!$obj) {
            $obj = new static;
            $obj->tag_id = $tagId;
            $obj->media_id = $mediaId;
            $obj->save();
        }

        return $obj;
    }
}
