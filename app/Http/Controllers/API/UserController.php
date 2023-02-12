<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getUserInfo(Request $req)
    {
        return $req->user();
    }

    public function getActiveUsers() 
    {
        return User::withCount('media')->public()
            ->orderBy('media_count', 'desc')
            ->get();
    }
}
