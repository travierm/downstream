<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    public static function findOrCreate($id) {
        $obj = static::where('name', $name)->first();
        return $obj ?: new static;
    }
}
