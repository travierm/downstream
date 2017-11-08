<?php

namespace App\Http\Controllers;

use Auth;
use App\UserCollection;
use Illuminate\Http\Request;

class CollectionAPIController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return [1,2,3];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return true;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
      $like = UserCollection::where([
        'user_id' => $userId,
        'table' => $table,
        'index' => $index
      ])->first();

      if($like) {
        return response()->json([
          'code'      =>  200,
          'message'   =>  "already collected this video"
        ], 200);
      }

      $like = new UserCollection;
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
