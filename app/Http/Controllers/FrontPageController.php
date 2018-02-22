<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use Auth;
use Cache;
use App\Media;

use Illuminate\Http\Request;


class FrontPageController extends Controller
{
  private $rowRenderIndex = [];

  public function index()
  {
    $latestVideos = Media::byType('youtube')
      ->limit(4)
      ->orderBy('id', 'DESC')
      ->get();

    if(Cache::get('showLatestVideos') == 'yes') {
      $this->createCustomRow("Latest Videos", $latestVideos);
    }

    $this->createRow("House Beats", [
      'Yellow Claw Party' => 26,
      'Apollo' => 27,
      'Snakehips' =>  18,
      'Locked Up' => 16
    ]);

    $this->createRow("Chill Beats", [
      'Still got you' => 30,
      "actually i'm good" => 29,
      'Where we go' =>  14,
      'Home' => 22
    ]);

    $this->createRow("Lil Peep", [
      //Display name for code reading => media_id to fetch video (used in code)
      'Beamer Boy' => 1,
      '19' => 2,
      'Spotlight' => 3,
      'Shooting Star' => 4
    ]);

    return view('frontpage.index', [
      'rows' => $this->rowRenderIndex
    ]);
  }

  private function prepareMediaItems($items = []) 
  { 
    if(Auth::check()) {
      $items = Media::addUserCollectedProp($items);
    }

    foreach($items as &$item) {
      $item->meta = json_decode($item->meta);

      $user = User::find($item->user_id);
      $user->smallHash = $user->shrinkHash();
      $user->profileLink = "/user/" . $user->hash . "/profile";

      $item->user = $user;
    }

    return $items;
  }

  private function createCustomRow($title = false, $media = false)
  {
    if(!$media) {
      return false;
    }

    $media = $this->prepareMediaItems($media);

    $row = [
      'title' => $title,
      'media' => $media
    ];

    $this->rowRenderIndex[] = $row;
  }

  private function createRow($title, $mediaIds) {
    //basic struct
    $row = [
      'title' => $title,
      'media' => []
    ];

    $orderedIds = implode(',', $mediaIds);

    //get media items by id
    $row['media'] = Media::whereIn('id', array_values($mediaIds))
      ->orderByRaw(DB::raw("FIELD(id, $orderedIds)"))
      ->get();

    if(count($row['media']) <= 0) {
      //no media items to display
      return false;
    }

    $row['media'] = $this->prepareMediaItems($row['media']);

    //push to render index
    $this->rowRenderIndex[] = $row;
  }
}
