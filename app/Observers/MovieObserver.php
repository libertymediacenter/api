<?php

namespace App\Observers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MovieObserver
{

    public function creating(Movie $movie)
    {

    }

    /**
     * Handle the movie "created" event.
     *
     * @param  \App\Models\Movie $movie
     * @return void
     */
    public function created(Movie $movie)
    {

    }

    /**
     * Handle the movie "updated" event.
     *
     * @param  \App\Models\Movie $movie
     * @return void
     */
    public function updated(Movie $movie)
    {
        //
    }

    public function saving(Movie $movie)
    {

    }

    /**
     * Handle the movie "deleted" event.
     *
     * @param  \App\Models\Movie $movie
     * @return void
     */
    public function deleted(Movie $movie)
    {
        //
    }

    /**
     * Handle the movie "restored" event.
     *
     * @param  \App\Models\Movie $movie
     * @return void
     */
    public function restored(Movie $movie)
    {
        //
    }

    /**
     * Handle the movie "force deleted" event.
     *
     * @param  \App\Models\Movie $movie
     * @return void
     */
    public function forceDeleted(Movie $movie)
    {
        //
    }
}
