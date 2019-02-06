<?php

namespace App\Services\Libraries;

use Adrenth\Thetvdb\Model\SeriesImageQueryResult;
use App\MetadataAgents\TheTVDBMetadataAgent;
use App\Models\Season;
use App\Models\Show;
use App\Services\AssetDownloadService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function findOrCreateShow(string $title, string $libraryId)
    {
        try {
            $show = Show::whereSlug(str_slug($title))->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            $metadata = $this->theTvDbMetadataClient->getShow($title);
            $metadata = $metadata->getData()->first();

            $show = Show::create([
                'title'      => $metadata->getSeriesName(),
                'slug'       => str_slug($metadata->getSlug()),
                'summary'    => $metadata->getOverview(),
                'start_year' => Carbon::parse($metadata->getFirstAired())->year,
                'status'     => $metadata->getStatus(),
                'thetvdb_id' => $metadata->getId(),
                'library_id' => $libraryId,
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
}
