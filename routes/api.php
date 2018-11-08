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
Route::get('/media/collection/{random?}', 'MediaAPIController@collection');
Route::get('/media/profile/{hash}', 'MediaAPIController@profile');
Route::get('/stat/library/size', 'StatAPIController@getLibrarySize');
Route::get('/stat/user/count', 'StatAPIController@getUserCount');
Route::get('/stat/queue/count', 'StatAPIController@getGlobalQueueCount');

Route::post('/media/discover', 'MediaAPIController@discover');
Route::post('/media/discoverables', 'MediaAPIController@getUserDiscoverables');

//Auto Resolved Media Actions
Route::post('/theater/{mediaId}', 'TheaterController@queueFromMediaId');
Route::get('/media/collect', 'MediaAPIController@resolve');
Route::get('/media/remove', 'MediaAPIController@resolve');
Route::get('/media/toss', 'MediaAPIController@resolve');
Route::post('/media/add', 'MediaAPIController@resolve');
Route::post('/media/search', 'MediaAPIController@resolve');

//API
Route::namespace('API')->group(function () {
    //Global
    Route::post('/global/push', 'GlobalController@postQueue');

    //Collection Routes

    //authed user collection
    Route::get('/collection/{random?}', "CollectionController@getCollection");
    Route::get('/user/collection/{random?}', "CollectionController@getUserCollection");

    //playlist
    Route::get('/playlists/collection/preview', 'PlaylistController@getCollectionPreview');
    //media
    Route::get('/media/{id}', 'MediaController@getMediaById');
    //user
    Route::get('/user/info', 'UserController@getUserInfo');
});

