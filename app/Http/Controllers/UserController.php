<?php

namespace App\Http\Controllers;

use Auth;
use App\Media;
use App\UserMedia;
use App\MediaRemoteReference;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
