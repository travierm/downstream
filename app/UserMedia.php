<?php

namespace App;

use Auth;
use App\Media;
use Illuminate\Database\Eloquent\Model;

class UserMedia extends Model
{
  protected $table = 'user_media';

  public static function collection($type = false)
  {
    $mediaIds = self::all()->pluck('media_id');

    $query = Media;
    if($type)
      $query = Media::where('type', $type);

    return $query::find($mediaIds);
  }

  public static function didCollect($mediaId)
  {
    self::where('media_id', $mediaId)
      ->where('user_id', Auth::user()->id)
      ->exists();
  }
}
