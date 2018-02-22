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

Route::get('/frontpage', 'FrontPageAPIController@index');

Route::get('/media/index', 'MediaAPIController@index');
Route::get('/media/collected', 'MediaAPIController@collected');
Route::get('/media/collection', 'MediaAPIController@collection');
Route::get('/media/profile/{hash}', 'MediaAPIController@profile');
Route::get('/stat/library/size', 'StatAPIController@getLibrarySize');
Route::get('/stat/user/count', 'StatAPIController@getUserCount');

//Auto Resolved Media Actions
Route::post('/theater/{mediaId}', 'TheaterController@queueFromMediaId');
Route::get('/media/collect', 'MediaAPIController@resolve');
Route::get('/media/remove', 'MediaAPIController@resolve');
Route::get('/media/toss', 'MediaAPIController@resolve');
Route::post('/media/add', 'MediaAPIController@resolve');
Route::post('/media/discover', 'MediaAPIController@resolve');
Route::post('/media/search', 'MediaAPIController@resolve');
