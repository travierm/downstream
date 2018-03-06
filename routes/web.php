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
  Route::get('/admin/dash', 'AdminController@index');
  Route::post('/admin/dash/settings', 'AdminController@postServerSettings');
});

Route::get('/', 'FrontPageController@index');

//Search
Route::get('/search', 'SearchController@getIndex');
Route::post('/search', 'SearchController@postSearchYouTube');

//Import
Route::get('/import', 'ImportController@getIndex');
Route::post('/import', 'ImportController@postImportVideo');

Route::get('/hash', 'UserController@getHash');

Auth::routes();

Route::get('/logout', 'UserController@logout');

Route::get('/{vue_capture?}', function () {
    return view('index');
})->where('vue_capture', '[\/\w\.-]*');
