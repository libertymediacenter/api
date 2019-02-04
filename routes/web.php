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

Route::get('/', function () {

});

Route::get('player/{type}/{slug}', 'PlayerController@show')->name('player');

Route::get('stream/{path}', 'StreamController@show')
    ->name('stream');

Route::get('stream/playlist/{path}', 'StreamController@getPlaylist')
    ->name('stream-playlist');

Route::get('/movies', function () {
    $movies = \App\Models\Movie::all();

    $movies->each(function (\App\Models\Movie $movie) {
        echo '<a href="' . route('player', [
                'type' => 'movie', 'slug' => $movie->slug,
            ]) . '"><img style="width: 100px;" src="/storage/' . $movie->poster . '" /></a>';
        dump($movie->toArray());
    });
});

Route::get('/storage/transcode/{path}', 'StreamController@getSegment');
