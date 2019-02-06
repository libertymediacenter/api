<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLibraryRequest;
use App\Http\Requests\LibraryCreateRequest;
use App\Http\Requests\LibraryUpdateRequest;
use App\Http\Resources\LibraryResource;
use App\Models\Library;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $libraries = Library::groupBy(['type', 'id'])->paginate();

        return LibraryResource::collection($libraries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\LibraryCreateRequest $request
     * @return \App\Http\Resources\LibraryResource
     */
    public function store(LibraryCreateRequest $request)
    {
        $library = Library::create($request->validated());

        LibraryResource::withoutWrapping();

        return new LibraryResource($library);
    }

    /**
     * Display the specified resource.
     *
     * @param  string $id
     * @return \App\Http\Resources\LibraryResource
     */
    public function show(string $id)
    {
        $library = Library::whereId($id)->firstOrFail();

        LibraryResource::withoutWrapping();

        return new LibraryResource($library);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\LibraryUpdateRequest $request
     * @param  string $id
     * @return \App\Http\Resources\LibraryResource
     */
    public function update(LibraryUpdateRequest $request, string $id)
    {
        $library = Library::whereId($id)->firstOrFail();
        $library->update($request->validated());

        LibraryResource::withoutWrapping();

        return new LibraryResource($library);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(string $id)
    {
        $library = Library::whereId($id)->firstOrFail();

        $library->delete();
    }
}
