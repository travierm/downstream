<?php

namespace App;

use App\UserMedia;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
  protected $fillable = ['id', 'origin', 'type', 'index', 'subtype', 'meta', 'user_id'];

  public static function addUserCollectedProp($rows)
  {
    foreach($rows as &$media)
    {
      $media->collected = UserMedia::didCollect($media->id);
    }

    return $rows;
  }

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

  public static function byType($type)
  {
    return self::where('type', $type);
  }

  public static function findByType($type, $index)
  {
    return self::where('type', $type)
      ->where('index', $index);
  }
}
