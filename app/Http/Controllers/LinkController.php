<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function viewMediaItem($index)
    {
        $media = Media::findByType('youtube', $index)->first();
        $recommendations = [];

        if($media) {
            $media = Media::prepareItemMeta($media);
        } else {
            dd("Bad Media ID");
        }


        $recommendations = $media->getReferences();

        return view('link.view-media', [
              'media' => $media,
              'recommendations' => $recommendations
        ]);
    }
}
