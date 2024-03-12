<?php

namespace App\Models;

use App\Models\UserCollection;
use Auth;
use Illuminate\Database\Eloquent\Model;

class YouTubeVideo extends Model
{
    public $table = 'youtube_videos';

    public static function cleanSearchResults($videoIds)
    {
        $results = [];
        foreach ($videoIds as $vid) {
            $result = new \stdClass();
            $video = self::findByVID($vid);

            $result->imported = false;
            $result->collected = false;
            $result->vid = $vid;
            if ($video) {
                $result->imported = true;
                $result->collected = UserCollection::didLikeVideo($video->id);
                $result->id = $video->id;
            } else {
                $result->id = uniqid();
            }

            $results[] = $result;
        }

        return $results;
    }

    public static function findByVID($vid)
    {
        return self::where('vid', $vid)->first();
    }

    public static function isDuplicate($vid)
    {
        return self::where('vid', $vid)->exists();
    }

    public static function getUserVideos()
    {
        return self::where('user_id', Auth::user()->id)->get();
    }
}
