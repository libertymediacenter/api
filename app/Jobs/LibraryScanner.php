<?php

namespace App\Jobs;

use App\MetadataAgents\FileMetadataAgent;
use App\Models\Library;
use App\Models\MediaContainer;
use App\Models\Movie;
use App\Models\Video;
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
    /** @var \App\MetadataAgents\FileMetadataAgent */
    private $fileMetadataAgent;

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
        $this->fileMetadataAgent = new FileMetadataAgent();

        $libraries = Library::all();

        $libraries->each(function (Library $library) {
            if ($library->type === 'movie') {
                $this->scanMovieLibrary($library);
            }
        });

        $this->delete();

        return null;
    }

    private function scanMovieLibrary(Library $library)
    {
        $directories = $this->getItemDirectories($library->path);
        $videos = $this->getVideoFiles($directories);

        $movies = collect([]);

        $videos->each(function (array $video) use (&$movies) {
            $path = $this->filesystem->path($video['path']);

            $movies->push([
                'file'    => $video,
                'streams' => $this->fileMetadataAgent->get($path),
            ]);
        });


        $movies->each(function ($movie) use (&$library) {
            $this->createMovie($movie, $library->id);
        });
    }

    private function createMovie($data, string $libraryId)
    {
        $movie = Movie::create([
            'title'      => $data['file']['filename'],
            'slug'       => str_slug($data['file']['filename']),
            'library_id' => $libraryId,
        ]);

        MetadataLookup::dispatch($movie);

        $mediaContainer = MediaContainer::create([
            'media_id'   => $movie->id,
            'media_type' => Movie::class,
            'path'       => $data['file']['path'],
            'bitrate'    => $data['streams']['format']->get('bit_rate'),
            'duration'   => (int)$data['streams']['format']->get('duration'),
            'size'       => $data['streams']['format']->get('size'),
        ]);

        /** @var Collection $streams */
        $streams = $data['streams'];

        $streams['video']->each(function ($video) use (&$mediaContainer) {
            Video::create([
                'media_container_id' => $mediaContainer->id,
                'display_name'       => '',
                'stream_index'       => $video['streamIndex'],
                'bitrate'            => $video['bitrate'],
                'framerate'          => $video['framerate'],
                'height'             => $video['height'],
                'width'              => $video['width'],
                'codec'              => $video['codec'],
                'chroma_location'    => $video['chromaLocation'],
                'color_range'        => $video['colorRange'],
                'profile'            => $video['profile'],
                'scan_type'          => $video['scanType'],
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
