<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
  public function getHash()
  {
    $hash = Auth::user()->hash;

    return view('user.hash', [
      'hash' => $hash
    ]);
  }

  public function logout()
  {
    Auth::logout();

    return redirect('/');
  }
}
