<?php

namespace App\MetadataAgents;

use App\Exceptions\NotImplementedException;
use App\MetadataAgents\Contracts\MetadataAgentInterface;
use App\Services\Metadata\Providers\TheTVDBProvider;

class TheTVDBMetadataAgent implements MetadataAgentInterface
{
    private $client;

    public function __construct()
    {
        $provider = new TheTVDBProvider();

        $this->client = $provider->getClient();
    }

    /**
     * @param string $title
     * @param array $options
     * @throws \App\Exceptions\NotImplementedException
     */
    public function getMovie(string $title, array $options = [])
    {
        throw new NotImplementedException('This provider does not support movies');
    }

    /**
     * @param string $title
     * @param array $options
     * @return \Adrenth\Thetvdb\Model\SeriesData
     * @throws \Adrenth\Thetvdb\Exception\RequestFailedException
     * @throws \Adrenth\Thetvdb\Exception\UnauthorizedException
     */
    public function getShow(string $title, array $options = [])
    {
        if (array_key_exists('imdb_id', $options)) {
            $res = $this->client->search()->seriesByImdbId($options['imdb_id']);
        } else {
            $series = $this->client->search()->seriesByName($title)->getData()->first();
            $res = $this->client->series()->get($series->getId());
        }

        return $res;
    }

    /**
     * @param int $id
     * @param $keyType
     * @return string|null
     */
    public function getImage(int $id, $keyType): ?string
    {
        try {
            if ($keyType === 'poster') {
                $images = $this->client->series()->getImagesWithQuery($id, [
                    'keyType' => $keyType,
                ])->getData();

                $poster = $images->first()->getFileName();
            }

            if ($keyType === 'episodes') {
                $poster = $this->client->episodes()->get($id)->getFilename();
            }

            if ($keyType === 'season') {
                //$images = $this->client->series()->get($id);

                //dd($images);

                return null;
            }

            if ($poster) {
                return 'https://www.thetvdb.com/banners/' . $poster;
            }
        } catch (\Exception $exception) {
            return null;
        }

        return null;
    }

    /**
     * @param int $showId
     * @param array $query
     * @return \Adrenth\Thetvdb\Model\SeriesEpisodesQuery
     * @throws \Adrenth\Thetvdb\Exception\RequestFailedException
     * @throws \Adrenth\Thetvdb\Exception\UnauthorizedException
     */
    public function getEpisodes(int $showId, array $query)
    {
        return $this->client->series()->getEpisodesWithQuery($showId, $query);
    }

    public function getSeason(string $imdbId, array $options = [])
    {
    }
}
