<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string $type
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(string $type, string $slug)
    {
        $movie = \App\Models\Movie::whereSlug($slug)->with('media')->firstOrFail();
        $path = storage_path("media/{$movie->media->path}");
        $streamPath = base64_encode($path);

        return view('player', [
            'stream' => "/stream/playlist/{$streamPath}.m3u8",
            'startStreamRoute' => route('stream', ['path' => $streamPath]),
            'video' => [
                'title' => $movie->title,
                'year' => $movie->year,
                'summary' => $movie->summary,
                'poster' => '/storage/' . $movie->poster,
                'imdbId' => 'tt' . $movie->imdb_id,
                'imdbRating' => $movie->imdb_rating,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
