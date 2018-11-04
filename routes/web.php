<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/hashMe', function() {
  return genUniqueHash(20);
});

Route::middleware(['admin'])->group(function() {
  Route::get('/admin/test/player', "AdminController@getTestPlayer");
  Route::get('/admin/engine', 'AdminController@getEngineFeed');
  Route::get('/admin/dash', 'AdminController@index');
  Route::post('/admin/dash/settings', 'AdminController@postServerSettings');

  Route::get('/admin/engine/clean', 'AdminController@getEngineClean');
  Route::get('/admin/media/edit', 'AdminController@getEngineClean');

  Route::get('/admin/filter/title', 'FilterController@getTitle');
  Route::post('/admin/filter/title', 'FilterController@postTitle');

  Route::get('/admin/toplist', 'Admin\ToplistController@getIndex');
  Route::get('/admin/toplist/update', 'Admin\ToplistController@getUpdate');
  Route::get('/admin/toplist/clear', 'Admin\ToplistController@getClear');
  Route::get('/admin/toplist/by/{sort}', 'Admin\ToplistController@getIndex');
  Route::get('/admin/toplist/media/visible/{tempItemId}/{isVisible}', 'Admin\ToplistController@getMediaTempVisible');
});

Route::get('/search', 'SearchController@getIndex');
//Route::get('/search', 'SearchController@postSearchYouTube');

Route::get('/', 'FrontPageController@getLanding');
Route::get('/all', 'FrontPageController@index');

//Link Sharing
//View a media item
Route::get('/v/{index}', 'LinkController@viewMediaItem');
//Backward compat for old links
Route::get('/media/{index}', 'LinkController@viewMediaItem');

//Import
Route::get('/import', 'ImportController@getIndex');
Route::post('/import', 'ImportController@postImportVideo');

Route::get('/user', 'UserController@getIndex');
Route::get('/test/video', 'MediaAPIController@testVideo');

Auth::routes();

Route::get('/logout', 'UserController@logout');

Route::get('/{vue_capture?}', function () {
    return view('index');
})->where('vue_capture', '[\/\w\.-]*');
