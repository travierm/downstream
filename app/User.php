<?php

namespace App;

use Hash;
use App\Theme;
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
        'hash', 'display_name', 'email', 'password', 'api_token', 'type', 'settings'
    ];

    private $defaultSettings = [
      'theme' => 'downstream_default'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'email'
    ];

    public function media()
    {
      return $this->hasMany('App\UserMedia');
    }

    public function isAdmin()
    {
      return ($this->type == 'admin');
    }

    public function getNavDisplayName()
    {
      $name = $this->display_name;
      return $name;
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

    public function themeOption($key)
    {
      $themeValue = @$this->getSettings()->theme[$key];

      if(!$themeValue) {
        return false;
      }

      return $themeValue;
    }

    public function setSetting($key, $value) {
      $settings = $this->getSettings();
      $settings->{$key} = $value;

      $this->settings = json_encode($settings);
      $this->save();
    }

    public function getSettings()
    {
      
      if(!$this->settings) {
        $this->setDefaultSettings();
      }

      $settings = json_decode($this->settings);
      $settings->theme = Theme::getById($settings->theme);

      return $settings;
    }

    public function updatePassword($newPassword)
    {
      $this->password = Hash::make($newPassword);

      return $this->save();
    }

    private function setDefaultSettings()
    {
      $this->settings = json_encode($this->defaultSettings);
      $this->save();
    }
}
