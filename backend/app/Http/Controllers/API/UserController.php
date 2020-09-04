<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function getUserInfo(Request $req)
    {
        return $req->user();
    }
}
