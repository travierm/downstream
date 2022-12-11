<?php

namespace App\Models;

use App\Media;
use App\UserMedia;
use Illuminate\Database\Eloquent\Model;

class MediaTempItem extends Model
{
    protected $table = 'media_temp_items';

    protected $fillable = ['source', 'source_id', 'title', 'index', 'thumbnail', 'visible', 'meta'];

    public function collected($userId)
    {
        $tempIndex = $this->index;

        $media = Media::where('index', $tempIndex)->first();
        if (! $media) {
            return false;
        }

        $userMedia = UserMedia::where('user_id', $userId)->where('id', $media->id)->get();

        if ($userMedia) {
            return true;
        } else {
            return false;
        }
    }
}
