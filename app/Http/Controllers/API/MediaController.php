<?php

namespace App\Http\Controllers\API;

use App\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function getMediaById($id) 
    {
        $item = Media::find($id);

        return $item;
    }
}
