<?php

namespace App\MetadataAgents;


use App\MetadataAgents\Contracts\MetadataAgentInterface;
use Tmdb\Laravel\Facades\Tmdb;

class TmdbMetadataAgent implements MetadataAgentInterface
{
    public function getMovie(string $imdbId, array $options = [])
    {
        // TODO: Implement getMovie() method.
    }

    public function getShow(string $imdbId, array $options = [])
    {
        // TODO: Implement getShow() method.
    }

    public function getSeason(string $imdbId, array $options = [])
    {
        // TODO: Implement getSeason() method.
    }

    public function getBackdropByImdbId(string $imdbId)
    {
        try {
            $res = Tmdb::getFindApi()->findBy($imdbId, ['external_source' => 'imdb_id']);
        } catch (\Exception $exception) {
            // could not find title.

            return;
        }

        return 'https://image.tmdb.org/t/p/w500' . $res['movie_results'][0]['backdrop_path'];
    }
}
