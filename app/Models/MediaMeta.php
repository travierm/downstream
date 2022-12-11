<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaMeta extends Model
{
    protected $table = 'media_meta';

    protected $primaryKey = 'media_id';

    public static function findAndFormat($mediaId)
    {
        $meta = static::where('media_id', $mediaId)->first();

        if (! $meta) {
            return false;
        }

        return $meta->formatted();
    }

    private function formatted()
    {
        $this->artist = '';
        $this->album = '';

        return $this;
    }
}
