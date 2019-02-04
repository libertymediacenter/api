<?php

namespace App\Services\Encoding;

class FFMpegService
{
    /** @var \FFMpeg\FFMpeg */
    private $ffmpeg;

    private $ffprobe;

    public function __construct()
    {
        $config = [
            'ffmpeg.binaries' => config('ffmpeg.binaries.ffmpeg', '/usr/bin/ffmpeg'),
            'ffprobe.binaries' => config('ffmpeg.binaries.ffprobe', '/usr/bin/ffprobe'),
            'ffprobe.threads' => config('ffmpeg.threads', 0),
            'timeout' => config('ffmpeg.timeout'),
        ];

        $this->ffmpeg = \FFMpeg\FFMpeg::create($config);

        $this->ffprobe = \FFMpeg\FFProbe::create($config);
    }

    /**
     * @return \FFMpeg\FFProbe
     */
    public function probe()
    {
        return $this->ffprobe;
    }

    /**
     * @return \FFMpeg\FFMpeg
     */
    public function ffmpeg()
    {
        return $this->ffmpeg;
    }
}
