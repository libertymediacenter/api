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

Route::get('/movies', 'MovieController@index');
Route::get('/movies/{slug}', 'MovieController@show');

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
    Route::get('/{type}/{slug}', 'StreamController@show');

    Route::get('/{path}', 'StreamController@show')
        ->name('stream');

    Route::get('/playlist/{path}', 'StreamController@getPlaylist')
        ->name('stream-playlist');
});
