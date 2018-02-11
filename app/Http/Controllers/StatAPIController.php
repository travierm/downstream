<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Media;
use App\User;

class StatAPIController extends Controller
{
 	public function __construct() 
 	{

 	}

 	public function getLibrarySize() 
 	{
 		$count = Media::count();

 		return response()->json([
 			'count' => $count
 		], 200);
 	}

 	public function getUserCount() 
 	{
 		$count = User::count();

 		return response()->json([
 			'count' => $count
 		], 200);
 	}
}
