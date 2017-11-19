<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
  protected $fillable = ['origin', 'type', 'index', 'subtype', 'meta', 'user_id'];

  public static function returnOrCreate($data)
  {
    $media = self::findByType($data['type'], $data['index'])->first();
    if(!$media) {
      $media = new static($data);
      $media->save();
    }

    return $media;
  }

  public static function isDuplicate($type, $index)
  {
    return self::where('type', $type)
      ->where('index', $index)
      ->exists();
  }

  public static function findByType($type, $index)
  {
    return self::where('type', $type)
      ->where('index', $index);
  }
}
