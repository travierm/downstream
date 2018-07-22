<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['tag_id', 'media_id'];

    public static function findOrCreate($name) {
        $obj = static::where('name', $name)->first();
        if($obj)
            $obj = static::find($obj->id);
            
        return $obj ?: new static;
    }

    public static function firstOrCreate($name) 
    {
        $obj = static::where('name', $name)->first();

        if(!$obj) {
            $obj = new static;
            $obj->name = $name;
            $obj->save();
        }

        return $obj;
    }
}
