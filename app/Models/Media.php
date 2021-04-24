<?php

namespace App\Models;

use Auth;
use App\Models\MediaMeta;
use App\Models\UserMedia;
use App\MediaRemoteReference;
use App\MediaType\YoutubeVideo;
use App\Services\Sources\SpotifyTrack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
  use SoftDeletes;
  
  protected $fillable = [
    'id', 
    'origin', 
    'type', 
    'index', 
    'subtype', 
    'title',
    'thumbnail',
    'user_id'
  ];

  protected $hidden = [
    'created_at'
  ];

  public function meta() {
    return $this->hasOne(MediaMeta::class);
  }

  public static function boot() {
    parent::boot();

    static::created(function($media) {

    });

    static::deleted(function($media) { // before delete() method call this
         $media->meta()->delete();
    });
  }

  public static function findByType($type, $index)
  {
    return self::where('type', $type)
      ->where('index', $index)
      ->first();
  }

  public static function createFromYoutubeVideo(YoutubeVideo $video, Array $meta = [])
  {
    $data = [
      'origin' => @$meta['origin'] ?: 'youtube#search',
      'type' => 'youtube',
      'subtype' => 'video',
      'index' => $video->videoId,
      'title' => $video->title,
      'thumbnail' => $video->thumbnail,
      'user_id' => $meta['user_id']
    ];

    $media = self::findByType('youtube', $video->videoId);
    if(!$media) {
      $media = new static($data);
      $media->save();
    }

    return $media;
  }

  public static function addUserCollectedProp($rows)
  {
    foreach($rows as &$media)
    {
      $media->collected = UserMedia::didCollect($media->id);
    }

    return $rows;
  }

  public function getOrFindSpotifyId()
  {
    if($this->spotify_id) {
      return $this->spotify_id;
    }

    $spotifyId = SpotifyTrack::findIdByTitle($this->title);

    // spotify_id becomes id of first item found when searching by title
    if($spotifyId) {
      $this->spotify_id = $spotifyId;
      $this->save();
    }else{
      return false;
    }

    return $this->spotify_id;
  }

  public function getReferences()
  {
    return MediaRemoteReference::where('media_id', $this->id)->get();
  }

  public function getMeta()
  {
    return json_decode($this->meta);
  }

  public function getMetaAttribute($value) 
  {
    $meta = MediaMeta::findAndFormat($this->id);

    if($meta) {
      return $meta;
    }

    return $value;
  }
}
