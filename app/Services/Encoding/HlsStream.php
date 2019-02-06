<?php

namespace App\Services\Encoding;

use App\Services\Encoding\Filters\HLSFilter;
use App\Services\Encoding\Formats\X264_AAC_HLS;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class HlsStream
{
    private $encoder;

    public function __construct()
    {
        $this->encoder = new Encoder();
    }

    /**
     * @param string $base64Path
     * @param int|float $seekOffset
     * @return string
     */
    public function getPlaylist(string $base64Path, $seekOffset): string
    {
        $base64Path = Str::before($base64Path, '.');

        $filePath = base64_decode($base64Path);
        $duration = $this->encoder->getDuration($filePath);

        $m3u8 = '#EXTM3U' . PHP_EOL;
        $m3u8 .= '#EXT-X-VERSION:3' . PHP_EOL;
        $m3u8 .= '#EXT-X-PLAYLIST-TYPE:VOD' . PHP_EOL;
        $m3u8 .= '#EXT-X-TARGETDURATION:' . (int)($seekOffset + 1) . PHP_EOL;
        $m3u8 .= '#EXT-X-MEDIA-SEQUENCE:0' . PHP_EOL;
        #$m3u8 .= '#EXT-X-INDEPENDENT-SEGMENTS' . PHP_EOL;

        for ($i = 0; $i < $duration; $i += $seekOffset) {
            $fileNo = $i;
            $offset = $seekOffset;

            if ($duration - $i < $seekOffset) {
                $offset = $duration - $i;
            }

            $offset = number_format((float)$offset, 3);

            $m3u8 .= '#EXTINF:' . $offset . ',' . PHP_EOL;

            if ($i > 0) {
                $m3u8 .= '#EXT-X-DISCONTINUITY' . PHP_EOL;
            }

            $m3u8 .= asset("/storage/transcode/{$base64Path}_{$fileNo}.ts") . PHP_EOL;
        }

        $m3u8 .= '#EXT-X-ENDLIST';

        return $m3u8;
    }

    /**
     * @param string $path
     * @param string $audioCodec
     * @param $startTime
     * @param $endTime
     * @return bool
     */
    public function transcodeSegment(string $path, string $audioCodec, $startTime, $endTime): bool
    {
        Redis::set("transcoding:{$path}", true);

        $filePath = base64_decode(Str::before($path, '_'));
        $file = $this->encoder->ffmpeg->open($filePath);

        $clip = $file
            ->clip(TimeCode::fromSeconds($startTime), TimeCode::fromSeconds($endTime));

        $format = new X264_AAC_HLS();
        $format->setPasses(1);
        $format->setKiloBitrate(1200);
        $format->setAudioKiloBitrate(192);
        $format->setAdditionalParameters([
            '-preset', 'veryfast',
        ]);

        $clip
            ->addFilter(new HLSFilter());

        $clip->save($format, storage_path("app/public/transcode/$path"));

        Redis::set("transcoding:{$path}", false);

        return true;
    }

    /**
     * @param string $base64Path
     * @param int $seekOffset
     * @param string $audioCodec
     * @return string
     */
    public function getSegment(string $base64Path, int $seekOffset, string $audioCodec): string
    {
        $offset = (int)Str::before(Str::after($base64Path, '_'), '.ts');

        try {
            $this->getSegmentFile($base64Path);
        } catch (FileNotFoundException $exception) {

            $ready = $this->transcodeSegment($base64Path, $audioCodec, $offset, $seekOffset);

            if ($ready) {
                sleep(1);
            }
        }

        return storage_path("app/public/transcode/$base64Path");
    }

    /**
     * @param $path
     * @return mixed
     */
    private function getSegmentFile($path)
    {
        return File::get(storage_path("app/public/transcode/$path"));
    }
}
