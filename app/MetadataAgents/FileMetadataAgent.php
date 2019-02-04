<?php

namespace App\MetadataAgents;

use App\Services\Encoding\FFMpegService;
use FFMpeg\FFProbe\DataMapping\Stream;
use Illuminate\Support\Collection;

class FileMetadataAgent
{
    private $probe;

    public function __construct()
    {
        $ffmpegService = new FFMpegService();

        $this->probe = $ffmpegService->probe();
    }

    public function get(string $filePath, $config = []): array
    {
        $videoStreams = collect([]);
        $audioStreams = collect([]);
        $subtitleStreams = collect([]);

        $streams = $this->probeStreams($filePath);

        $streams->each(function (Stream $stream) use (&$videoStreams, &$audioStreams, &$subtitleStreams) {
            switch ($stream->get('codec_type')) {
                case 'video':
                    $videoStreams->push($this->formatVideoStream($stream));
                    break;
                case 'audio':
                    $audioStreams->push($this->formatAudioStream($stream));
                    break;
                case 'subtitle':
                    $subtitleStreams->push($this->formatSubtitleStream($stream));
                    break;
            }
        });

        return [
            'format' => $this->probe->format($filePath),
            'video' => $videoStreams,
            'audio' => $audioStreams,
            'subtitle' => $subtitleStreams,
        ];
    }

    private function probeStreams(string $filePath): Collection
    {
        $streams = $this->probe->streams($filePath)->all();
        $streams = collect($streams);

        return $streams;
    }

    private function formatVideoStream(Stream $stream): array
    {
        $framerate = explode('/', $stream->get('r_frame_rate'));
        $framerate = (float)str_split((string)((int)$framerate[0] / (int)$framerate[1]), 5)[0];

        $tags = $stream->get('tags', []);

        return [
            'type' => 'video',
            'profile' => $stream->get('profile'),
            'codec' => $stream->get('codec_name'),
            'width' => $stream->get('width'),
            'height' => $stream->get('height'),
            'chromaLocation' => $stream->get('chroma_location'),
            'scanType' => $stream->get('field_order'),
            'colorRange' => $stream->get('pix_fmt'),
            'langCode' => $tags['language'] ?? null,
            'referenceFrames' => $stream->get('refs'),
            'framerate' => $framerate,
            'bitrate' => $stream->get('bit_rate'),
            'streamIndex' => $stream->get('index'),
        ];
    }

    private function formatAudioStream(Stream $stream): array
    {
        $tags = $stream->get('tags', []);

        return [
            'type' => 'audio',
            'codec' => $stream->get('codec_name'),
            'sampleRate' => $stream->get('sample_rate'),
            'channels' => $stream->get('channels'),
            'channelLayout' => $stream->get('channel_layout'),
            'bitrate' => $stream->get('bit_rate'),
            'langCode' => $tags['language'] ?? null,
            'streamIndex' => $stream->get('index'),
        ];
    }

    private function formatSubtitleStream(Stream $stream): array
    {
        $tags = $stream->get('tags', []);

        return [
            'type' => 'subtitle',
            'codec' => $stream->get('codec_name'),
            'langCode' => $tags['language'] ?? null,
            'streamIndex' => $stream->get('index'),
        ];
    }
}
