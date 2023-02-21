<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUserInfo(Request $req)
    {
        return $req->user();
    }

    public function getActiveUsers()
    {
        return User::withCount('media')
            ->public()
            //->whereNot('id', Auth::user()->id)
            ->orderBy('media_count', 'desc')
            ->limit(9)
            ->get();
    }
}
