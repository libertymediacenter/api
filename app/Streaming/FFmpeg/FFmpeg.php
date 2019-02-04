<?php

namespace App\Streaming\FFmpeg;

class FFmpeg
{
    private $ffmpegBinary;
    private $ffprobeBinary;

    public function __construct()
    {
        $this->ffmpegBinary = config('ffmpeg.binaries.ffmpeg');
        $this->ffprobeBinary = config('ffmpeg.binaries.ffprobe');
    }
}
