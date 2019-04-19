<?php
use Auth;
use DB;
use App\User;
use App\Media;

$_user_display_names = [];

function getUserDisplayName($userId) {
    global $_user_display_names;

    if($_user_display_names[$userId]) {
        return $_user_display_names[$userId];
    }

    $user = User::where('id', $userId)->first();
    $displayName = $user->display_name;

    if(!@$displayName) {
        return false;
    }

    $_user_display_names[$userId] = $displayName;

    return $displayName;
}

function getMediaByIds($ids = []) 
{
    $items = Media::byType('youtube')
            ->whereIn('id', $ids)
            ->get();

    if(Auth::check()) {
      Media::addUserCollectedProp($items);
    }

    $items->map(function(&$item) {
      $item->meta = json_decode($item->meta);
    }, $items);

    return $items;
}
?>