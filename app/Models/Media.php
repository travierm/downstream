<?php

namespace App\Models;

use Auth;
use App\Models\MediaMeta;
use App\Models\UserMedia;
use App\MediaRemoteReference;
use App\MediaType\YoutubeVideo;
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
    'meta', 
    'user_id'
  ];

  protected $hidden = [
    'created_at'
  ];

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
      'user_id' => $meta['user_id'],
      'meta' => json_encode([])
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