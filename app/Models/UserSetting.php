<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $table = 'user_settings';
    protected $fillable = ['media_id', 'user_id'];

    private static $defaults = [
        "resetOverPause" => false
    ];

    public static function findByKey($key)
    {
        $userId = Auth::user()->id;
        if(!$userId) {
            throw new Exception("Could not get user_id from Auth");
        }

        return self::where('key', $key)
            ->where('user_id', $userId)
            ->first();
    }
}
