<?php

namespace App\Http\Controllers\Admin;

use App\Media;
use App\UserMedia;
use App\GlobalQueue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GlobalQueueController extends Controller
{
    public function getLatestMedia()
    {
        $items = UserMedia::collection();

        $collection = [];
        foreach($items as $media) {

            if($media->meta) {
                $media->meta = json_decode($media->meta);
                //collected will always be true
                $media->collected = true;
                $media->globalQueued = GlobalQueue::mediaIsQueued($media->id);
            }

            $collection[] = $media;
        }

        return view('admin.globalqueue', compact('collection'));
    }
}
