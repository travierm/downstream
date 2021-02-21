<?php

namespace Tests;

use App\Models\Media;
use App\Models\UserMedia;

class UserData {
  private static $userId;

  public static function setUser($userId)
  {
    self::$userId = $userId;
  }

  public static function getFirstCollectedItem()
  {
    $userMedia = UserMedia::where('user_id', self::$userId)
      ->orderBy('created_at' , 'DESC')
      ->first();

    return Media::find($userMedia->media_id);
  }
}
