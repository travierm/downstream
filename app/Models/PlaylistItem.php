<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlaylistItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'media_id',
        'created_by',
        'playlist_id'
    ];
}
