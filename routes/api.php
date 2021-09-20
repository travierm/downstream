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

// Controllers in the API Folder only
Route::namespace('API')->group(function () {
    
    Route::get('/ping', function() {
        return "pong";
    });

    Route::get('/ana/media/stats', 'AnalyticsController@getStats');
    
    Route::post('/auth/login', 'Auth\LoginController@postLogin');

    /* Authenticated routes only */
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/search/{query}', 'SearchController@getResults');
        Route::get('/search/autocomplete/{query}', "SearchAutocompleteController@getResults");

        // Discover
        Route::get('/discover/track/{videoId}', 'DiscoverTrackController@similarTracks');

        // User Collection
        Route::get('/collection', "CollectionController@getCollection");


        Route::get('/video/{videoId}', 'MediaController@getVideoByIndex');

        Route::post('/media/push/{mediaId}', 'MediaCollectionController@pushItem');
        Route::post('/media/collect', 'MediaCollectionController@postCollectItem');
        Route::delete('/media/collection/{itemId}', 'MediaCollectionController@removeItemFromCollection');

        // Playlists
        Route::get('/playlist/all', 'PlaylistController@getAllLists');
        Route::post('/playlist/create', 'PlaylistController@createList');
        Route::delete('/playlist/delete/{playlistId}', 'PlaylistController@deleteList');
        
        // Item Routes
        Route::get('/playlist/{playlistId}', 'PlaylistController@getListItems');
        Route::post('/playlist/add', 'PlaylistController@addItem');
        Route::delete('/playlist/{playlistId}/delete/{mediaId}', 'PlaylistController@deleteItem');

        // Analytics
        Route::get('/ana/media/play/{mediaId}', 'AnalyticsController@recordUserPlay');

    });

    /*
    // Analytics
    Route::post('/ana/media/play', 'AnalyticsController@recordUserPlay');

    // Following API
    Route::get('/follow/{follow_id}', "FollowerController@follow");
    Route::get('/unfollow/{follow_id}', "FollowerController@unfollow");
    Route::get('/followers', "FollowerController@getFollowers");

    // Playlist
    Route::get('/playlists/collection/preview', 'PlaylistController@getCollectionPreview');;

    // Spotify
    Route::get('/spotify/url', 'SpotifyController@getAuthorizeUrl');
    Route::get('/spotify/disable', 'SpotifyController@disableAccess');

    // Demo
    Route::get('/demo/search', 'DemoController@searchQuery');
    
    */
});

