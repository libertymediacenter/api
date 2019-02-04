<?php

namespace App\MetadataAgents\Contracts;

interface MetadataAgentInterface
{
    public function getMovie(string $imdbId, array $options = []);

    public function getShow(string $imdbId, array $options = []);

    public function getSeason(string $imdbId, array $options = []);
}
