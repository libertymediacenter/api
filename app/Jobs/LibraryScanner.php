<?php

namespace App\Jobs;

use App\MetadataAgents\FileMetadataAgent;
use App\MetadataAgents\TheTVDBMetadataAgent;
use App\Models\Episode;
use App\Models\Library;
use App\Models\MediaContainer;
use App\Models\Movie;
use App\Models\Video;
use App\Services\Libraries\ShowService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use App\Services\FFMpegService;
use Illuminate\Support\Str;

class LibraryScanner implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Illuminate\Filesystem\FilesystemAdapter */
    private $filesystem;
    /** @var \App\MetadataAgents\FileMetadataAgent */
    private $fileMetadataAgent;
    /** @var TheTVDBMetadataAgent */
    private $theTvDbMetadataAgent;
    /** @var ShowService */
    private $showService;

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
    public function handle(): void
    {
        $this->filesystem = \Storage::disk('media');
        $this->fileMetadataAgent = new FileMetadataAgent();
        $this->theTvDbMetadataAgent = new TheTVDBMetadataAgent();
        $this->showService = new ShowService();

        $libraries = Library::all();

        $libraries->each(function (Library $library) {
            if ($library->type === 'tv') {
                $this->scanTvLibrary($library);
            }

            if ($library->type === 'movie') {
                $this->scanMovieLibrary($library);
            }
        });

        $this->delete();
    }

    /**
     * @param \App\Models\Library $library
     */
    private function scanTvLibrary(Library $library)
    {
        $shows = collect([]);
        $showsDir = $this->getItemDirectories($library->path);

        $showsDir->each(function ($show) use (&$shows) {
            $seasons = $this->getItemDirectories($show['path']);
            $episodes = $this->getVideoFiles($seasons, true);

            $episodes->each(function ($episode) use ($shows) {
                $path = $this->filesystem->path($episode['path']);

                $shows->push([
                    'file'    => $episode,
                    'streams' => $this->fileMetadataAgent->get($path),
                ]);
            });
        });

        $shows->each(function ($episode) use (&$library) {
            $this->createEpisode($episode, $library->id);
        });
    }

    /**
     * @param $data
     * @param string $libraryId
     * @throws \Adrenth\Thetvdb\Exception\RequestFailedException
     * @throws \Adrenth\Thetvdb\Exception\UnauthorizedException
     */
    private function createEpisode($data, string $libraryId)
    {
        $file = $data['file'];
        /** @var Collection $streams */
        $streams = $data['streams'];

        $regex = '/[Ss]([0-9]+)[Ee]([0-9]+)/';
        preg_match_all($regex, $file['filename'], $matches);

        $season = (int)$matches[1][0];
        $episode = (int)$matches[2][0];

        $showTitle = Str::before($file['filename'], '-');

        $show = $this->showService->findOrCreateShow($showTitle, $libraryId);

        /** @var  $episodeMetadata */
        $episodeMetadata = $this->theTvDbMetadataAgent->getEpisodes($show->thetvdb_id, [
            'airedSeason'  => $season,
            'airedEpisode' => $episode,
        ])->getData()->first();

        $seasonNo = $episodeMetadata->getAiredSeason();

        $season = $this->showService->findOrCreateSeason($show->id, $seasonNo);
        $episode = $this->showService->findOrCreateEpisode($season, $episodeMetadata);

        $mediaContainer = MediaContainer::create([
            'media_id'   => $episode->id,
            'media_type' => Episode::class,
            'path'       => $file['path'],
            'bitrate'    => $streams['format']->get('bit_rate'),
            'duration'   => (int)$streams['format']->get('duration'),
            'size'       => $streams['format']->get('size'),
        ]);

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

    /**
     * @param \App\Models\Library $library
     */
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

    /**
     * @param $data
     * @param string $libraryId
     */
    private function createMovie($data, string $libraryId)
    {
        $movie = Movie::create([
            'title'      => $data['file']['filename'],
            'slug'       => str_slug($data['file']['filename']),
            'library_id' => $libraryId,
        ]);

        MovieMetadataLookup::dispatch($movie);

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

    /**
     * @param string $directory
     * @return \Illuminate\Support\Collection
     */
    private function getItemDirectories(string $directory): Collection
    {
        $fileDir = collect($this->filesystem->listContents($directory));

        $directories = $fileDir->map(function ($item) {
            if ($item['type'] === 'dir') {
                return $item;
            }
        })->filter();

        return $directories;
    }

    /**
     * @param \Illuminate\Support\Collection $directories
     * @param bool $recursive
     * @return \Illuminate\Support\Collection
     */
    private function getVideoFiles(Collection $directories, $recursive = false): Collection
    {
        $videos = collect([]);

        $directories->each(function ($dir) use (&$videos, $recursive) {
            $listing = collect($this->filesystem->listContents($dir['path']));

            if (!$recursive) {
                $file = $listing->first(function ($file) {
                    return $this->getVideo($file);
                });

                if ($file) {
                    $videos->push($file);
                }
            } else {
                $listing->each(function ($file) use (&$videos) {
                    $file = $this->getVideo($file);

                    if ($file) {
                        $videos->push($file);
                    }
                });
            }
        });

        return $videos;
    }

    /**
     * @param $file
     * @return mixed
     */
    private function getVideo($file)
    {
        if (\in_array($file['extension'], $this->videoFileExtensions, true)) {
            return $file;
        }
    }
}
