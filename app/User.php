<?php

namespace App;

use App\UserLike;
use App\YouTubeVideo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash', 'display_name', 'email', 'password', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    public function media()
    {
      return $this->hasMany('App\UserMedia');
    }

    public function getNavDisplayName()
    {
      $name = $this->display_name;
      $string = false;
      if($name) {
        $string = $name . "(" . $this->shrinkHash() . ")";
      }else{
        $string = $this->shrinkHash();
      }

      return $string;
    }

    public function shrinkHash()
    {
      return substr($this->hash, 0, 8);
    }

    public static function likedYouTubeVideos()
    {
      $videoIndexes = UserLike::where('user_id', Auth::user()->id)
        ->where('table', 'youtube_videos')
        ->pluck('index');

      return YouTubeVideo::whereIn('id', $videoIndexes)->get();
    }
}
