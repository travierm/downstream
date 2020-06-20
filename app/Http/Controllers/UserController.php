<?php

namespace App\Http\Controllers;

use Auth;
use DateTime;
use App\User;
use App\UserSpotifyToken;
use App\Services\SpotifyAPI;
use App\Media;
use App\UserMedia;
use App\MediaRemoteReference;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  
  public function getGuidePage()
  {
    return view('user.guide');
  }

  /**
   * Process successfully authorization from Spotify
   * Save tokens to the database
   */
  public function getConnect(Request $request)
  {
      $code = $request->input('code');

      $session = SpotifyAPI::getSession();

      $session->requestAccessToken($code);
      $accessToken = $session->getAccessToken();
      $refreshToken = $session->getRefreshToken();

      $userId = Auth::user()->id;

      $token = UserSpotifyToken::where('user_id', $userId);
      if($token) {
        //delete existing tokens
        $token = UserSpotifyToken::where('user_id', $userId)->delete();
      }

      $token = new UserSpotifyToken();
      $token->access_token = $accessToken;
      $token->refresh_token = $refreshToken;
      $token->user_id = $userId;
      $token->save();

      if($token) {
        $success = true;
      }else{
        $success = false;
      }

      return view('user.spotify-import', [
        'success' => $success
      ]);
    }

    public function getSpotifyImportPage() 
    {
      $userId = Auth::user()->id;

      $authorized = UserSpotifyToken::where("user_id", $userId)->exists();

      return view("user.spotify-import", [
        'success' => $authorized
      ]);
    }

    public function getSpotifyConnectLink()
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
      'sinceAccountCreation' => "$daysSince days ago on " . date("F j, Y, g:i a", strtotime(Auth::user()->created_at))
    ]);
  }

  public function getUserList()
  {
    $users = User::where('private', false)->orderBy("created_at", "DESC")->get();

    //build custom view user struct

    $displayUsers = collect();
    foreach($users as &$user) {
      
      $user->following = @Auth::user()->isFollowing($user->id);

      $user->collectionSize = UserMedia::where('user_id', $user->id)->count();

      if($user->collectionSize == 0) {
        continue;
      }

      $latestMediaItemID = UserMedia::where('user_id', $user->id)
        ->orderBy("created_at", "DESC")
        ->limit(1)
        ->pluck('media_id');

      $user->thumbnail = false;
      if($latestMediaItemID) {
        $media = Media::find($latestMediaItemID)->first();
        
        if(@$media) {
          $user->thumbnail = $media->getMeta()->thumbnail;
        }
      }

      $displayUsers->push($user);
    }

    return view('user.list', [
      'users' => $displayUsers->sortByDesc("collectionSize")->all()
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

  public function logout()
  {
    Auth::logout();

    return redirect('/');
  }
}
