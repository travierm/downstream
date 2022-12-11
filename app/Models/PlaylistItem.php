<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaylistItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'media_id',
        'created_by',
        'playlist_id',
    ];

    public static function findOrCreate($userId, $playlistId, $mediaId)
    {
        $data = [
            'created_by' => $userId,
            'playlist_id' => $playlistId,
            'media_id' => $mediaId,
        ];

        $item = self::where($data)->first();

        if (! $item) {
            return self::create($data);
        }

        return $item;
    }
}
