<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\Auth\LoginController;

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




// OLD ROUTES

/*Route::post("/youtube/collect", "YouTubeAPIController@collect");
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

//Git Webhook Deployment
Route::post('/deploy', 'DeployController@deploy');
*/

// Controllers in the API Folder only
Route::namespace('API')->group(function () {
    Route::post('/auth/login', 'Auth\LoginController@postLogin');

    /* Authenticated routes only */
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/search/{query}', 'SearchController@getResults');
        Route::get('/search/autocomplete/{query}', "SearchAutocompleteController@getResults");

        // User Collection
        Route::get('/collection', "CollectionController@getCollection");
        Route::post('/media/collect', 'MediaCollectionController@postCollectItem');
        Route::delete('/media/collection/{itemId}', 'MediaCollectionController@removeItemFromCollection');
    });

    /*
    //Analytics
    Route::post('/ana/media/play', 'AnalyticsController@recordUserPlay');

    //Global
    Route::post('/global/push', 'GlobalController@postQueue');

    // Streaming Routes
    Route::post('/stream/search', 'StreamController@postSearchResults');

    //Collection Routes

    //authed user collection
    
    Route::get('/collection/remove/{mediaId}', "CollectionController@removeItem");
    Route::get('/user/collection/{random?}', "CollectionController@getUserCollection");

    //Following API
    Route::get('/follow/{follow_id}', "FollowerController@follow");
    Route::get('/unfollow/{follow_id}', "FollowerController@unfollow");
    Route::get('/followers', "FollowerController@getFollowers");

    //playlist
    Route::get('/playlists/collection/preview', 'PlaylistController@getCollectionPreview');
    //media
    Route::get('/media/{id}', 'MediaController@getMediaById');
    Route::post('/media/update', 'MediaController@postUpdate');
    //user
    Route::get('/user/info', 'UserController@getUserInfo');

    //Radio
    Route::get('/radio/seed', 'RadioController@getSeedResults');

    //Spotify
    Route::get('/spotify/url', 'SpotifyController@getAuthorizeUrl');
    Route::get('/spotify/disable', 'SpotifyController@disableAccess');

    //Demo
    Route::get('/demo/search', 'DemoController@searchQuery');
    */
});

