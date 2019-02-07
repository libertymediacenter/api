<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShowResource;
use App\Models\Show;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @return \App\Http\Resources\ShowResource
     */
    public function show(string $slug)
    {
        $show = Show::whereSlug($slug)
            ->with(['seasons', 'seasons.episodes', 'genres', 'cast'])
            ->firstOrFail();

        ShowResource::withoutWrapping();

        return new ShowResource($show);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
