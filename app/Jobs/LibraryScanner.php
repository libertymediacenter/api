<?php

namespace App\Jobs;

use App\MetadataAgents\FileMetadataAgent;
use App\Models\MediaContainer;
use App\Models\Movie;
use App\Models\Video;
use FFMpeg\FFProbe\DataMapping\Stream;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use App\Services\FFMpegService;

class LibraryScanner implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Illuminate\Filesystem\FilesystemAdapter */
    private $filesystem;

    private $videoFileExtensions = [
        'mkv',
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->filesystem = \Storage::disk('media');
        $fileMetadataAgent = new FileMetadataAgent();

        $directories = $this->getItemDirectories('movies');
        $videos = $this->getVideoFiles($directories);

        $movies = collect([]);

        $videos->each(function (array $video) use (&$movies, $fileMetadataAgent) {
            $path = $this->filesystem->path($video['path']);

            $movies->push([
                'file' => $video,
                'streams' => $fileMetadataAgent->get($path),
            ]);
        });


        $movies->each(function ($movie) {
            $this->createMovie($movie);
        });
    }

    private function createMovie($data)
    {
        $movie = Movie::create([
            'title' => $data['file']['filename'],
            'slug' => str_slug($data['file']['filename']),
        ]);

        MetadataLookup::dispatch($movie);

        $mediaContainer = MediaContainer::create([
            'media_id' => $movie->id,
            'media_type' => Movie::class,
            'path' => $data['file']['path'],
            'bitrate' => $data['streams']['format']->get('bit_rate'),
            'duration' => (int)$data['streams']['format']->get('duration'),
            'size' => $data['streams']['format']->get('size'),
        ]);

        /** @var Collection $streams */
        $streams = $data['streams'];

        $streams['video']->each(function ($video) use (&$mediaContainer) {
            Video::create([
                'media_container_id' => $mediaContainer->id,
                'display_name' => '',
                'stream_index' => $video['streamIndex'],
                'bitrate' => $video['bitrate'],
                'framerate' => $video['framerate'],
                'height' => $video['height'],
                'width' => $video['width'],
                'codec' => $video['codec'],
                'chroma_location' => $video['chromaLocation'],
                'color_range' => $video['colorRange'],
                'profile' => $video['profile'],
                'scan_type' => $video['scanType'],
            ]);
        });
    }

    private function getItemDirectories(string $directory)
    {
        $fileDir = collect($this->filesystem->listContents($directory));

        $directories = $fileDir->map(function ($item) {
            if ($item['type'] === 'dir') {
                return $item;
            }
        })->filter();

        return $directories;
    }

    private function getVideoFiles(Collection $directories)
    {
        $videos = collect([]);

        $directories->each(function ($dir) use (&$videos) {
            $listing = collect($this->filesystem->listContents($dir['path']));

            $file = $listing->first(function ($file) {
                if (\in_array($file['extension'], $this->videoFileExtensions, true)) {
                    return $file;
                }
            });

            if ($file) {
                $videos->push($file);
            }
        });

        return $videos;
    }
}
