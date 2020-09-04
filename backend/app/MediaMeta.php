<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaMeta extends Model
{
    protected $primaryKey = 'media_id';
    protected $table = "media_meta";

    public static function findAndFormat($mediaId) 
    {
        $meta = static::where('media_id', $mediaId)->first();

        if(!$meta) {
            return false;
        }

        return $meta->formatted();
    }

    private function formatted() {
        $this->artist = "";
        $this->album = "";

        return $this;
    }
}
