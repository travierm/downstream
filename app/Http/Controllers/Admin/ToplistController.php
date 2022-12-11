<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MediaTempItem;
use Artisan;

class ToplistController extends Controller
{
    public function getIndex($sort = 'creation')
    {
        $items = MediaTempItem::orderBy('created_at', 'ASC');

        if ($items) {
            //found temp items
            if ($sort == 'visible') {
                //sort by visible
                $items->where('visible', 1);
            } else {
                $items->where('visible', 0);
            }

            //db fetch
            $items = $items->get();
        } else {
            //no temp items found
            $items = [];
        }

        return view('admin.toplist.index', [
            'items' => $items,
            'sort' => $sort,
        ]);
    }

    public function getUpdate()
    {
        Artisan::call('spotify:toplist');

        return back()->withInput();
    }

    public function getClear()
    {
        MediaTempItem::where('source', 'spotify:toplist')->delete();

        return back()->withInput();
    }

    public function getMediaTempVisible($tempItemId, $isVisible)
    {
        $item = MediaTempItem::find($tempItemId);
        if ($item) {
            $item->visible = $isVisible;
            $item->save();
        }

        return back()->withInput();
    }
}
