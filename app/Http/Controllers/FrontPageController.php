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
      $this->createCustomRow("Newly Discovered", $latestVideos);
    }

    $this->createRow("Bobby Tarantino ||", [
      'Rick and Morty' => 95,
      'Overnight' => 93,
      'Midnight' => 94,
      'Indica' => 91,
      //Row 2
      'Yuck' => 96,
      'Wassup' => 97,
      'Contra' => 98,
      '44 more' => 100
    ]);

    $this->createRow("Chill", [
      "Hey i miss you" => 81,
      "Resonance" => 22,
      "Wizards" => 85,
      "Fly" => 33
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
