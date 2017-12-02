<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

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

  public function logout()
  {
    Auth::logout();

    return redirect('/');
  }
}
