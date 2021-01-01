<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    public static function findOrCreate($name) {
        trim($name);

        $obj = Artist::where('name', $name)->first();
    
        if(!$obj) {
            $obj = new Artist();
            $obj->name = $name;
            $obj->save();
        }

        return $obj;
    }
}
