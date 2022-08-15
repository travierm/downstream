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

// Controllers in the API Folder only
Route::namespace('API')->group(function () {
    Route::get('/ping', function () {
        return "pong";
    });

    Route::get('/ana/media/stats', 'AnalyticsController@getStats');

    // Auth & User Routes
    Route::post('/auth/login', 'Auth\LoginController@postLogin');
    Route::post('/user/register', 'User\UserRegistrationController@registerUser');
    Route::post('/waitlist/signup', 'User\UserRegistrationController@createWaitListSignup');



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
        Route::get('/collection/{userHash}', "CollectionController@getCollectionByHash");

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

        // Spotify
        Route::get('/spotify/stats', 'SpotifyController@getUserStats');
        Route::post('/spotify/connect', 'SpotifyController@getConnect');
        Route::get('/spotify/authorize', 'SpotifyController@getAuthorizeUrl');
        Route::get('/spotify/disable', 'SpotifyController@getDisable');
        Route::get('/spotify/run-sync', 'SpotifyController@runSpotifySync');

        // Following API
        Route::get('/followage', "FollowerController@getFollowage");
        Route::put('/follow/{followId}', "FollowerController@follow");
        Route::delete('/unfollow/{followId}', "FollowerController@unfollow");
    });

    /*
// Analytics
Route::post('/ana/media/play', 'AnalyticsController@recordUserPlay');

// Playlist
Route::get('/playlists/collection/preview', 'PlaylistController@getCollectionPreview');;

// Spotify
Route::get('/spotify/url', 'SpotifyController@getAuthorizeUrl');
Route::get('/spotify/disable', 'SpotifyController@disableAccess');

// Demo
Route::get('/demo/search', 'DemoController@searchQuery');

 */
});
