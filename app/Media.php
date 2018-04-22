<?php

namespace App;

use Auth;
use App\UserMedia;
use App\MediaRemoteReference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
  use SoftDeletes;
  
  protected $fillable = ['id', 'origin', 'type', 'index', 'subtype', 'meta', 'user_id'];

  /**
   * Adds collected prop and decodes meta json
   * Takes a single Media item or an array of Media items
   * returns either the single item or an array automagically
   * @param  array||object $items items to be prepared
   * @return array||object prepared items
   */
  public static function prepareItemMeta($items)
  {
    $returnArray = true;
    if(!is_array($items)) {
      $returnArray = false;

      $items = [$items];
    }


    if(Auth::check()) {
      self::addUserCollectedProp($items);
    }

    foreach($items as $item) {
      $item->meta = json_decode($item->meta);
    }

    if(!$returnArray) {
      return $items[0];
    }

    return $items;
  }

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

  public function getReferences()
  {
    return MediaRemoteReference::where('media_id', $this->id)->get();
  }

  public function getMeta()
  {
    return json_decode($this->meta);
  }
}
