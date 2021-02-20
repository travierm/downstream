<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    public function getListItems($playlistId)
    {

    }
    
    public function getAllLists()
    {

    }

    public function createList()
    {
        $userId = Auth::user()->id;
    }

    public function deleteList()
    {

    }

    public function addItem()
    {

    }

    public function deleteItem()
    {

    }
}
