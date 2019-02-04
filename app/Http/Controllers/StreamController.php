<?php

namespace App\Http\Controllers;

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

    public function getPlaylist(Request $request, string $base64Path)
    {
        $m3u8 = $this->hlsStream->getPlaylist($base64Path, $this->seekOffset);

        return response($m3u8, 200, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS, HEAD',
            'Access-Control-Allow-Headers' => 'Accept, Accept-Language, Authorization, Cache-Control, Content-Disposition, Content-Encoding, Content-Language, Content-Length, Content-MD5, Content-Range, Content-Type, Date, Host, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, Origin, OriginToken, Pragma, Range, Slug, Transfer-Encoding, Want-Digest',
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
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS, HEAD',
            'Access-Control-Allow-Headers' => 'Accept, Accept-Language, Authorization, Cache-Control, Content-Disposition, Content-Encoding, Content-Language, Content-Length, Content-MD5, Content-Range, Content-Type, Date, Host, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, Origin, OriginToken, Pragma, Range, Slug, Transfer-Encoding, Want-Digest',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public',
            //'Content-Type' => \File::mimeType($segmentPath),
            //'Content-Length' => \File::size($segmentPath),
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
