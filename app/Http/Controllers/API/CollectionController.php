<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }
}
