<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSpotifyToken extends Model
{
    protected $table = 'user_spotify_tokens';

    public static function findByUserID($userId) {
        return self::where('user_id', $userId)->first();
    }
}
