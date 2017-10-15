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

Route::get('/', 'AppController@getIndex');

//Search
Route::get('/search', 'SearchController@getIndex');
Route::post('/search', 'SearchController@postSearchYouTube');

//Import
Route::get('/import', 'ImportController@getIndex');
Route::post('/import', 'ImportController@postImportVideo');

//Collection
Route::get('/collection', 'CollectionController@getIndex');

Route::get('/hash', 'UserController@getHash');

Auth::routes();

Route::get('/logout', 'UserController@logout');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/home', 'HomeController@index')->name('home');
