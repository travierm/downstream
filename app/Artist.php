<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    public static function findOrCreate($name) {
        trim($name);

        $obj = static::where('name', $name)->get();

        $obj = static::find($obj->id);
        return $obj ?: new static;
    }
}
