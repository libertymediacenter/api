<?php

namespace App\Jobs;

use App\MetadataAgents\FileMetadataAgent;
use App\MetadataAgents\TheTVDBMetadataAgent;
use App\Models\Episode;
use App\Models\Library;
use App\Models\MediaContainer;
use App\Models\Movie;
use App\Models\Season;
use App\Models\Show;
use App\Models\Video;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
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
        $this->theTvDbMetadataAgent = new TheTVDBMetadataAgent();

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

        return null;
    }

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

        try {
            $show = Show::whereSlug(str_slug($showTitle))->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            $data = $this->theTvDbMetadataAgent->getShow($showTitle);
            $data = $data->getData()->first();

            $show = Show::create([
                'title'      => $data->getSeriesName(),
                'slug'       => str_slug($data->getSlug()),
                'summary'    => $data->getOverview(),
                'start_year' => Carbon::parse($data->getFirstAired())->year,
                'status'     => $data->getStatus(),
                'thetvdb_id' => $data->getId(),
                'library_id' => $libraryId,
            ]);

            $poster = $this->theTvDbMetadataAgent->getImage($data->getId(), 'poster');
            if ($poster) {
                $show->poster = $this->fetchPoster($poster, $show);
                $show->save();
            }
        }

        $episodeMetadata = $this->theTvDbMetadataAgent->getEpisodes($show->thetvdb_id, [
            'airedSeason'  => $season,
            'airedEpisode' => $episode,
        ])->getData()->first();

        $seasonNo = $episodeMetadata->getAiredSeason();

        try {
            $season = Season::whereShowId($show->id)->whereSeason($seasonNo)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            $season = Season::create([
                'show_id' => $show->id,
                'season'  => $seasonNo,
            ]);

//            $poster = $this->theTvDbMetadataAgent->getImage($seasonMetadata->getId(), 'seasons');
//            if ($poster) {
//                $this->fetchPoster($poster, $season);
//            }
        }

        $episode = Episode::create([
            'season_id'  => $season->id,
            'title'      => $episodeMetadata->getEpisodeName(),
            'summary'    => $episodeMetadata->getOverview(),
            'thetvdb_id' => $episodeMetadata->getId(),
        ]);

        $poster = $this->theTvDbMetadataAgent->getImage($episodeMetadata->getId(), 'episodes');
        if ($poster) {
            $episode->poster = $this->fetchPoster($poster, $episode);
            $episode->save();
        }

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

    private function getVideoFiles(Collection $directories, $recursive = false)
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

    private function getVideo($file)
    {
        if (\in_array($file['extension'], $this->videoFileExtensions, true)) {
            return $file;
        }
    }

    private function fetchPoster(string $url, $model)
    {
        $guzzle = new Client();

        try {
            $r = $guzzle->get($url);
            $image = $r->getBody();
        } catch (\Exception $exception) {
            dump($exception);

            return null;
        }

        $ext = \File::extension($url);
        $type = str_slug((new \ReflectionClass($model))->getShortName());
        $path = "assets/images/{$type}/{$model->id}.${ext}";

        \Storage::disk('local')->put("public/{$path}", $image);

        return $path;
    }
}
