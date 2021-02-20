<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'private',
        'created_by'
    ];

    public static function findOrCreate($userId, $name) {
        trim($name);

        $playlist = self::where([
            'name' => $name,
            'created_by' => $userId
        ])->first();
        
        return ($playlist ? $playlist : new static);
    }
}
