<?php

namespace App\MetadataAgents\Contracts;

interface RatingInterface
{
    public function getMovie(string $imdbId, string $lang = 'en-US');
}
