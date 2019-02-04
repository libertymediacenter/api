<?php

namespace App\Services\Encoding;

class Encoder
{
    public $ffmpeg;
    public $ffprobe;

    public function __construct()
    {
        $ffmpegService = new FFMpegService();

        $this->ffmpeg = $ffmpegService->ffmpeg();
        $this->ffprobe = $ffmpegService->probe();
    }

    public function getDuration(string $filePath)
    {
        return (float)$this->ffprobe->format($filePath)->get('duration');
    }
}
