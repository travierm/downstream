<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;

class LinkController extends Controller
{	
 	public function viewMediaItem($index)
 	{
 		$media = Media::findByType('youtube', $index)->first();

 		if($media) {
 			$media = Media::prepareItemMeta($media);
 		}

 		//dd($media);

 		return view('link.view-media', [
      		'media' => $media
    	]);
 	}
}
