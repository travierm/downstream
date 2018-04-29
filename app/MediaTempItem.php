<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaTempItem extends Model
{
    protected $table = "media_temp_items";
    protected $fillable = ['source', 'source_id', 'title', 'index', 'thumbnail', 'visible', 'meta'];
}
