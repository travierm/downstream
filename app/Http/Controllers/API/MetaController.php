<?php

namespace App\Http\Controllers\API;

use App\Media;
use App\MediaMeta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Handles API routes for Artists, Albums and MediaMeta
 */

class MetaController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function postUpdateMeta(Request $request) 
    {
        $medaId = $request->input('media_id');
    }
}
