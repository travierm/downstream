<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'private',
        'created_by',
    ];

    public function getItemCount()
    {
        return PlaylistItem::where('playlist_id', $this->id)->count();
    }

    public static function findOrCreate($userId, $name)
    {
        trim($name);

        $playlist = self::where([
            'name' => $name,
            'created_by' => $userId,
        ])->first();

        return $playlist ? $playlist : new static();
    }
}
