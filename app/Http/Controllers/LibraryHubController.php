<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Models\Movie;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class LibraryHubController extends Controller
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

    public function getMoviesHub(Request $request)
    {
        $movies = QueryBuilder::for(Movie::class)
            ->allowedFilters([
                Filter::scope('has_genre'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['title', 'created_at'])
            ->paginate();

        return MovieResource::collection($movies);
    }

    public function getShowsHub(Request $request)
    {

    }
}
