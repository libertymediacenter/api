<?php

return [
    'threads' => env('FFMPEG_THREADS', 4),
    'binaries' => [
        'ffmpeg' => env('FFMPEG_BINARY_PATH', '/usr/bin/ffmpeg'),
        'ffprobe' => env('FFPROBE_BINARY_PATH', '/usr/bin/ffprobe'),
    ],
];
