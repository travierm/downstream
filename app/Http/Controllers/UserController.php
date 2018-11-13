<?php

namespace App\Http\Controllers;

use Auth;
use DateTime;
use App\Media;
use App\UserMedia;
use App\MediaRemoteReference;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private function getSpotifyConnectLink()
    {
      $session = new SpotifyWebAPI\Session(
        env('SPOTIFY_CLIENT_ID'),
        env('SPOTIFY_CLIENT_SECRET'),
        "https://downstream.us/connect/spotify"
      );

      $options = [
          'scope' => [
              'playlist-read-private',
              'playlist-modify-private',
              'user-read-private',
          ],
      ];

      return $session->getAuthorizeUrl($options);
    }
  
  public function getIndex() {

    $hash = Auth::user()->hash;
    $displayName = Auth::user()->display_name;
    $collectionSize = count(UserMedia::collection());
    $collectionSpread = UserMedia::where("user_id","!=", Auth::user()->id)
      ->whereIn('media_id', UserMedia::pluckMediaIds())
      ->count();
    
    $datetime1 = new DateTime(Auth::user()->created_at);
    $datetime2 = new DateTime(date("Y-m-d H:i:s"));
    $interval = $datetime1->diff($datetime2);

    $daysSince = $interval->format('%a');

    return view('user.index', [
      'hash' => $hash,
      'displayName' => $displayName,
      'collectionSize' => $collectionSize,
      'collectionSpread' => $collectionSpread,
      'sinceAccountCreation' => date("F j, Y, g:i a", strtotime(Auth::user()->created_at)) . " <==> $daysSince days ago "
    ]);
  }

  public function getHash()
  {
    $hash = Auth::user()->hash;
    $displayName = Auth::user()->display_name;

    return view('user.hash', [
      'hash' => $hash,
      'displayName' => $displayName
    ]);
  }

  public function getDiscover()
  {
  }

  public function logout()
  {
    Auth::logout();

    return redirect('/');
  }
}
