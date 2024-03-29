<?php

namespace App\Models;

use DB;
use Auth;
use Hash;
use App\Theme;
use App\UserLike;
use App\YouTubeVideo;
use App\Models\UserMediaPlays;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash',
        'display_name',
        'email',
        'password',
        'api_token',
        'type',
        'settings',
        'private'
    ];

    private $defaultSettings = [
        'theme' => 'downstream_default',
    ];

    protected $casts = [
        'private' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    protected $appends = [
        'has_spotify_connection',
    ];

    public function scopePublic(Builder $builder)
    {
        return $builder->where('private', 0);
    }

    public function getHasSpotifyConnectionAttribute()
    {
        return UserSpotifyToken::where('user_id', $this->id)->exists();
    }

    // This function allows us to get a list of users following us
    public function followers()
    {
        return $this->belongsToMany('App\Models\User', 'followers', 'follow_id', 'user_id')->withTimestamps();
    }

    // Get all users we are following
    public function following()
    {
        return $this->belongsToMany('App\Models\User', 'followers', 'user_id', 'follow_id')->withTimestamps();
    }

    public function isFollowing($followId)
    {
        return Follow::where('user_id', $this->id)
            ->where('follow_id', $followId)
            ->exists();
    }

    public function userMediaPlays()
    {
        return $this->hasMany(UserMediaPlays::class);
    }

    public function media()
    {
        return $this->hasMany(UserMedia::class);
    }

    public function isAdmin()
    {
        return $this->type == 'admin';
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

    public function getRecentDiscoveredItemsCount()
    {
        $date = \Carbon\Carbon::today()->subDays(14);

        return DB::table('user_media')
            ->join('media_remote_references', 'user_media.media_id', '=', 'media_remote_references.media_id')
            ->where('user_id', Auth::user()->id)
            ->where('user_media.created_at', '>=', $date)
            ->count();
    }

    public function getActivityFeedCount($lastCheck = false)
    {
        $followingUserIds = Auth::user()->following()->pluck('follow_id');

        if ($lastCheck) {
            $date = $lastCheck;
        } else {
            $date = \Carbon\Carbon::today()->subDays(14);
        }

        $followingMediaCount = DB::table('user_media')
            ->whereIn('user_id', $followingUserIds)
            ->where('created_at', '>=', $date)
            ->orderBy('created_at', 'DESC')
            ->count();

        return $followingMediaCount;
    }

    public function themeOption($key)
    {
        $themeValue = @$this->getSettings()->theme[$key];

        if (! $themeValue) {
            return false;
        }

        return $themeValue;
    }

    public function setSetting($key, $value)
    {
        $settings = $this->getSettings();
        $settings->{$key} = $value;

        $this->settings = json_encode($settings);
        $this->save();
    }

    public function getSettings()
    {
        if (! $this->settings) {
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
