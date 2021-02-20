<?php

namespace App\Http\Controllers\API;

use App\Models\Playlist;
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

    public function createList(Request $request)
    {
        $name = $request->name;
        $userId = Auth::user()->id;

        $list = Playlist::findOrCreate($userId, $name);
        $list->name = $name;
        $list->created_by = $userId;
        $list->save();

        if($list->id) {
            return response()->json([
                'code' => 200,
                'message' => "created playlist"
            ]);
        }

        return response()->json([
            'code' => 500,
            'message' => "could not create playlist"
        ]);
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
