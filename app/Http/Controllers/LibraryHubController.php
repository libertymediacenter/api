<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Http\Resources\ShowResource;
use App\Models\Movie;
use App\Models\Show;
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
        $perPage = $request->query('perPage', 30);

        $movies = QueryBuilder::for(Movie::class)
            ->allowedFilters([
                'title',
                Filter::scope('has_genre'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['title', 'created_at'])
            ->paginate($perPage);

        return MovieResource::collection($movies);
    }

    public function getShowsHub(Request $request)
    {
        $perPage = $request->query('perPage', 30);

        $shows = QueryBuilder::for(Show::class)
            ->allowedFilters(['title'])
            ->defaultSort('-created_at')
            ->allowedSorts(['title', 'created_at'])
            ->paginate($perPage);

        return ShowResource::collection($shows);
    }
}
