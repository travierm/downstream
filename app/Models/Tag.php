<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['tag_id', 'media_id'];

    public static function firstOrCreate($name) 
    {
        $obj = static::where('name', $name)->first();

        if(!$obj) {
            $obj = new static;
            $obj->name = static::formatName($name);
            $obj->save();
        }

        return $obj;
    }

    private static function formatName($name) 
    {
        return strtolower($name);
    }
}
