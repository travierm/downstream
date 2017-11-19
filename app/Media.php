<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
  public static function findByType($type, $index)
  {
    return self::where('type', $type)
      ->where('index', $index);
  }
}
