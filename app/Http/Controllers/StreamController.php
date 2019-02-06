<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Services\Encoding\HlsStream;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StreamController extends Controller
{
    private $seekOffset = 10;
    private $hlsStream;

    public function __construct()
    {
        $this->hlsStream = new HlsStream();
    }

    /**
     * @param string $type
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $type, string $slug)
    {
        $movie = \App\Models\Movie::whereSlug($slug)->with('media')->firstOrFail();
        $path = storage_path("media/{$movie->media->path}");
        $streamPath = base64_encode($path);

        return response()->json([
            'stream' => "/stream/playlist/{$streamPath}.m3u8",
            'video'  => new MovieResource($movie),
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $base64Path
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getPlaylist(Request $request, string $base64Path)
    {
        $m3u8 = $this->hlsStream->getPlaylist($base64Path, $this->seekOffset);

        return response($m3u8, 200, [
            'Cache-Control' => 'public',
            'Content-Type'  => 'application/x-mpegURL',
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getSegment(Request $request, string $path)
    {
        $audioCodec = $request->query('audioCodec', 'libfdk_aac');

        $segmentPath = $this->hlsStream->getSegment($path, $this->seekOffset, $audioCodec);

        return response()->download($segmentPath, $path, [
            'Cache-Control' => 'public',
        ]);
    }
}
