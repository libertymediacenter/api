<?php

namespace App\Services\Encoding\Formats;

use FFMpeg\Format\Video\DefaultVideo;

class X264_AAC_HLS extends DefaultVideo
{
    /** @var integer */
    private $passes = 1;

    public function __construct($audioCodec = 'libfdk_aac', $videoCodec = 'libx264')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
    }

    /**
     * Returns the list of available audio codecs for this format.
     *
     * @return array
     */
    public function getAvailableAudioCodecs()
    {
        return [
            'libfdk_aac',
            'aac',
            'copy'
        ];
    }

    /**
     * Returns true if the current format supports B-Frames.
     *
     * @see https://wikipedia.org/wiki/Video_compression_picture_types
     *
     * @return Boolean
     */
    public function supportBFrames()
    {
        return false;
    }

    /**
     * Returns the list of available video codecs for this format.
     *
     * @return array
     */
    public function getAvailableVideoCodecs()
    {
        return [
            'libx264',
        ];
    }

    /**
     * @param $passes
     *
     * @return \App\Services\Encoding\Formats\X264_AAC_HLS
     */
    public function setPasses($passes)
    {
        $this->passes = $passes;
        
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPasses()
    {
        return $this->passes;
    }

    /**
     * @return int
     */
    public function getModulus()
    {
        return 2;
    }
}
