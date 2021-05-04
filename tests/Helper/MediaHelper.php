<?php

namespace Tests\Helper;

use App\Models\Media;

global $user;
 
class MediaHelper {
  public static function deleteByIndex(string $videoId)
  {
    Media::where('index', $videoId)->forceDelete();
  }
}