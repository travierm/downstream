<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;

class LinkController extends Controller
{	
 	public function viewMediaItem($index)
 	{
 		$media = Media::findByType('youtube', $index)->first();
 		$recommendations = [];

 		if($media) {
 			$media = Media::prepareItemMeta($media);
 		}

 		$recommendations = $media->getReferences();

 		return view('link.view-media', [
      		'media' => $media,
      		'recommendations' => $recommendations
    	]);
 	}
}
