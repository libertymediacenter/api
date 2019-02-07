<?php

namespace App\MetadataAgents;

use App\MetadataAgents\Contracts\MetadataAgentInterface;

class OmdbMetadataAgent implements MetadataAgentInterface
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
}
