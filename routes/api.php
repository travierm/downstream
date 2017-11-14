<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/youtube/collect", "YouTubeAPIController@collect");
Route::post("/youtube/toss", "YouTubeAPIController@toss");

Route::post("/test", "YouTubeAPIController@test");

//Route::resource('like', 'LikeController');
Route::get('collection', 'CollectionAPIController@index');

Route::get('/media/index', 'MediaAPIController@index');
Route::get('/media/collected', 'MediaAPIController@collected');

//Auto Resolved Media Actions
Route::get('/media/collect', 'MediaAPIController@resolve');
Route::get('/media/remove', 'MediaAPIController@resolve');
Route::post('/media/add', 'MediaAPIController@resolve');
Route::post('/media/discover', 'MediaAPIController@resolve');
