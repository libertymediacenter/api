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

    public function show(string $type, string $slug)
    {
        $movie = \App\Models\Movie::whereSlug($slug)->with('media')->firstOrFail();
        $path = storage_path("media/{$movie->media->path}");
        $streamPath = base64_encode($path);

        return response()->json([
            'stream' => "/stream/playlist/{$streamPath}.m3u8",
            'video' => new MovieResource($movie),
        ]);
    }

    public function getPlaylist(Request $request, string $base64Path)
    {
        $m3u8 = $this->hlsStream->getPlaylist($base64Path, $this->seekOffset);

        return response($m3u8, 200, [
            'Cache-Control' => 'public',
            'Content-Type' => 'application/x-mpegURL',
        ]);
    }

    public function getSegment(Request $request, string $path)
    {
        $offset = (int)Str::before(Str::after($path, '_'), '.ts');

        $audioCodec = $request->query('audioCodec', 'libfdk_aac');

        try {
            $this->getSegmentFile($path);
        } catch (FileNotFoundException $exception) {

            $ready = $this->transcodeSegment($path, $audioCodec, $offset);


            if ($ready) {
                sleep(1);
            }
        }

        $segmentPath = storage_path("app/public/transcode/$path");

        return response()->download($segmentPath, $path, [
            'Cache-Control' => 'public',
        ]);
    }

    /**
     * @param $path
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getSegmentFile($path)
    {
        return File::get(storage_path("app/public/transcode/$path"));
    }

    private function transcodeSegment(string $path, string $audioCodec, $offset)
    {
        return $this->hlsStream->transcodeSegment($path, $audioCodec, $offset, $this->seekOffset);
    }
}
