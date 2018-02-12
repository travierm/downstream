<?php

namespace App\Http\Controllers;

use Auth;
use App\Media;

use Illuminate\Http\Request;


class FrontPageController extends Controller
{
  private $rowRenderIndex = [];

  public function index()
  {
    $latestVideos = Media::byType('youtube')
      ->limit(4)
      ->orderByRaw('created_at DESC')
      ->get();

    $this->createCustomRow("Latest Videos", $latestVideos);

    $this->createRow("Lil Peep", [
      //Display name for code reading => media_id to fetch video (used in code)
      'Beamer Boy' => 1,
      '19' => 2,
      'Spotlight' => 3,
      'Shooting Star' => 4
    ]);

    $this->createRow("Lil Skies", [
      'Clique' => 8,
      'Red Rose' => 7,
      'Red' => 6,
      'Red ose' => 5,
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

    //get media items by id
    $row['media'] = Media::whereIn('id', array_values($mediaIds))
      ->orderBy('created_at', 'DESC')
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
