<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Media;
use App\UserMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlaylistController extends Controller
{
    function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function getCollectionPreview()
    {
        $collection = collect(UserMedia::collection());

        $items = $collection->take(24);

        return Media::prepareItemMeta($items->all());
    }
}
