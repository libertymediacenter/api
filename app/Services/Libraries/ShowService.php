<?php

namespace App\Services\Libraries;

use App\MetadataAgents\TheTVDBMetadataAgent;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Season;
use App\Models\Show;
use App\Services\AssetDownloadService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ShowService
{
    /** @var \App\MetadataAgents\TheTVDBMetadataAgent */
    private $theTvDbMetadataClient;
    /** @var \App\Services\AssetDownloadService */
    private $assetDownloadService;

    public function __construct()
    {
        $this->theTvDbMetadataClient = new TheTVDBMetadataAgent();
        $this->assetDownloadService = new AssetDownloadService();
    }

    /**
     * @param string $title
     * @param string $libraryId
     * @return \App\Models\Show|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws \Adrenth\Thetvdb\Exception\RequestFailedException
     * @throws \Adrenth\Thetvdb\Exception\UnauthorizedException
     */
    public function findOrCreateShow(string $title, string $libraryId)
    {
        try {
            $show = Show::whereSlug(str_slug($title))->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            $metadata = $this->theTvDbMetadataClient->getShow($title);

            $show = Show::create([
                'title'      => $metadata->getSeriesName(),
                'slug'       => str_slug($metadata->getSlug()),
                'summary'    => $metadata->getOverview(),
                'start_year' => Carbon::parse($metadata->getFirstAired())->year,
                'status'     => $metadata->getStatus(),
                'thetvdb_id' => $metadata->getId(),
                'library_id' => $libraryId,
                'imdb_id'    => $metadata->getImdbId(),
                'network'    => $metadata->getNetwork(),
                'runtime'    => $metadata->getRuntime(),
            ]);

            $poster = $this->theTvDbMetadataClient->getImage($metadata->getId(), 'poster');
            if ($poster) {
                try {
                    $show->poster = $this->assetDownloadService->fetchImage($poster, $show);
                    $show->save();
                } catch (\Exception $exception) {
                    Log::error('Could not download image!', ['exception' => $exception->getMessage()]);
                }
            }

            $genres = collect($metadata->getGenre());
            $this->attachGenres($genres, $show);
        }

        return $show;
    }

    /**
     * @param string $showId
     * @param int $seasonNo
     * @return mixed
     */
    public function findOrCreateSeason(string $showId, int $seasonNo)
    {
        try {
            $season = Season::whereShowId($showId)->whereSeason($seasonNo)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            $season = Season::create([
                'show_id' => $showId,
                'season'  => $seasonNo,
            ]);
        }

        return $season;
    }

    public function findOrCreateEpisode(Season $season, $metadata)
    {
        $episode = Episode::create([
            'season_id'  => $season->id,
            'title'      => $metadata->getEpisodeName(),
            'summary'    => $metadata->getOverview(),
            'thetvdb_id' => $metadata->getId(),
        ]);

        $poster = $this->theTvDbMetadataClient->getImage($metadata->getId(), 'episodes');
        if ($poster) {
            $episode->poster = $this->assetDownloadService->fetchImage($poster, $episode);
            $episode->save();
        }

        return $episode;
    }

    /**
     * @param \Illuminate\Support\Collection $genres
     * @param \App\Models\Show $show
     */
    private function attachGenres(Collection $genres, Show $show)
    {
        $genres->each(function (string $name) use (&$show) {
            $genre = Genre::firstOrCreate(['name' => $name]);

            $show->genres()->attach($genre);
        });
    }
}
