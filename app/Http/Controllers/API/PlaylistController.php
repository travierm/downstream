<?php

namespace App\Http\Controllers\API;


use App\Models\Media;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;

            return $next($request);
        });
    }

    public function getAllLists(Request $request)
    {
        $mediaId = $request->get('media_id');

        $userLists = Playlist::where(['created_by' => $this->userId])
            ->orderBy('created_at', 'DESC')
            ->get();

        $playlistIds = [];
        if ($mediaId) {
            $playlistIds = PlaylistItem::where([
                'media_id' => $mediaId,
                'created_by' => $this->userId
            ])->pluck('playlist_id')->all();
        }

        foreach ($userLists as $playlist) {

            if (in_array($playlist->id, $playlistIds)) {
                $playlist->itemAdded = true;
            } else {
                $playlist->itemAdded = false;
            }
        }

        return response()->json([
            'code' => 200,
            'items' => $userLists
        ]);
    }

    public function getListItems($playlistId)
    {
        $playlistMediaIds = PlaylistItem::where('playlist_id', $playlistId)
            ->orderBy('created_at', 'DESC')
            ->pluck('media_id');

        $mediaItems = Media::whereIn('id', $playlistMediaIds)
            ->get();

        if ($mediaItems) {
            return response()->json([
                'code' => 200,
                'items' => $mediaItems
            ]);
        }

        return response()->json([
            'code' => 500,
            'message' => 'could not fetch item of playlist' . $playlistId
        ]);
    }

    public function createList(Request $request)
    {
        $name = $request->name;
        $userId = Auth::user()->id;

        $list = Playlist::findOrCreate($userId, $name);
        $list->name = $name;
        $list->created_by = $userId;
        $list->save();

        if ($list->id) {
            return response()->json([
                'code' => 200,
                'playlist_id' => $list->id,
                'message' => "created playlist"
            ]);
        }

        return response()->json([
            'code' => 500,
            'message' => "could not create playlist"
        ]);
    }

    public function deleteList($playlistId)
    {
        $playlist = Playlist::findOrFail($playlistId);

        if ($playlist && $playlist->created_by == $this->userId) {
            $playlist->delete();

            return response()->json([
                'code' => 200,
                'message' => "deleted playlist"
            ]);
        }

        return response()->json([
            'code' => 500,
            'message' => "could not delete playlist"
        ]);
    }

    public function addItem(Request $request)
    {
        $mediaId = $request->input('media_id');
        $playlistId = $request->input('playlist_id');

        if (!$playlistId) {
            return response()->json([
                'code' => 500,
                'message' => "no playlist id given"
            ]);
        }

        $item = PlaylistItem::findOrCreate($this->userId, $playlistId, $mediaId);

        if (!$item) {
            return response()->json([
                'code' => 500,
                'message' => "could not add media_id to playlist item"
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => "added media_id to playlist item"
        ]);
    }

    public function deleteItem(Request $request)
    {
        $mediaId = $request->input('media_id');
        $playlistId = $request->input('playlist_id');

        $playlistItem = PlaylistItem::where([
            'created_by' => $this->userId,
            'playlist_id' => $playlistId,
            'media_id' => $mediaId
        ]);

        if ($playlistItem) {
            $playlistItem->delete();

            return response()->json([
                'code' => 200,
                'message' => "deleted playlist item"
            ]);
        }

        return response()->json([
            'code' => 500,
            'message' => "could not delete playlist ite,"
        ]);
    }
}
