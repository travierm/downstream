<?php

namespace App\Http\Controllers;

use Auth;
use App\UserLike;
use Illuminate\Http\Request;

class LikeController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function store(Request $request)
  {
    //store like
    $userId = Auth::guard('api')->user()->id;
    $table = $request->input('table');
    $index = $request->input('index');

    if(!$userId or !$table or !$index) {
      return response()->json([
            'code'      =>  401,
            'message'   =>  "Invalid data given."
        ], 401);
    }

    //dupe check
    $like = UserLike::where([
      'user_id' => $userId,
      'table' => $table,
      'index' => $index
    ])->first();

    if($like) {
      return response()->json([
        'code'      =>  200,
        'message'   =>  "already liked this video"
      ], 200);
    }

    $like = new UserLike;
    $like->user_id = $userId;
    $like->table = $table;
    $like->index = $index;

    $res = $like->save();
    //error checking in case of db connection issues
    if(!$res) {
      return response()->json([
            'code'      =>  401,
            'message'   =>  "Could not save like to database."
        ], 401);
    }else{
      return response()->json([
        'code'      =>  200,
        'message'   =>  "success"
      ], 200);
    }
  }
}
