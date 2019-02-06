<?php

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

/*
 * Media routes
 */

Route::prefix('movies')->group(function () {
    Route::get('', 'MovieController@index');
    Route::get('{slug}', 'MovieController@show');
});

Route::prefix('shows')->group(function () {
    Route::get('{slug}', 'ShowController@show');
});

/*
 * Hubs
 */

Route::prefix('hubs')->group(function () {
    Route::get('/movies', 'LibraryHubController@getMoviesHub')->name('hubs.movies.list');

    Route::get('/shows', 'LibraryHubController@getShowsHub')->name('hubs.shows.list');
});

/*
 * Libraries
 */

Route::prefix('libraries')->group(function () {
    Route::get('', 'LibraryController@index')->name('libraries.index');
    Route::get('{id}', 'LibraryController@show')->name('libraries.show');
    Route::post('', 'LibraryController@store')->name('libraries.store');
    Route::put('{id}', 'LibraryController@update')->name('libraries.update');
    Route::delete('{id}', 'LibraryController@destroy')->name('libraries.destroy');
});

/*
 * Stream Routes
 */

Route::prefix('stream')->group(function () {

    Route::get('{path}', 'StreamController@show')
        ->name('stream');

    Route::get('playlist/{path}', 'StreamController@getPlaylist')
        ->name('stream-playlist');


    Route::get('{type}/{slug}', 'StreamController@show');
});
