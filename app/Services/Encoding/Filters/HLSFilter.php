<?php

namespace App\Services\Encoding\Filters;

use FFMpeg\Filters\Video\VideoFilterInterface;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Media\Video;

class HLSFilter implements VideoFilterInterface
{
    /** @var integer */
    private $priority;

    public function __construct($priority = 100)
    {
        $this->priority = $priority;
    }

    /**
     * Returns the priority of the filter.
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Applies the filter on the the Video media given an format.
     *
     * @param Video $video
     * @param VideoInterface $format
     *
     * @return array An array of arguments
     */
    public function apply(Video $video, VideoInterface $format)
    {
        $commands = [
            '-sc_threshold', '2',
            '-x264opts:0', 'subme=0:me_range=4:rc_lookahead=10:me=dia:no_chroma_me:8x8dct=0:partitions=none:no-scenecut',
            '-vsync', '1',
            '-async', '48000',
            '-force_key_frames', 'expr:gte(t,n_forced*10)',
            '-vf', 'scale=-1:720',
            '-mixed-refs', '0',
            '-refs', '3',
            '-map_metadata', '-1',
            '-map_chapters', '-1',
            '-bsf:v', 'h264_mp4toannexb',
            '-pix_fmt', 'yuv420p',
            '-break_non_keyframes', '1',
            '-avoid_negative_ts', 'disabled',
            '-flags', '-global_header',
        ];

        return $commands;
    }
}
