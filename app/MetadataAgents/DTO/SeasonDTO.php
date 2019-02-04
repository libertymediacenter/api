<?php

namespace App\MetadataAgents\DTO;

class SeasonDTO
{
    /** @var int */
    public $number;
    /** @var string */
    public $posterUrl;

    /**
     * @param int $number
     * @return SeasonDTO
     */
    public function setNumber(int $number): SeasonDTO
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param string $posterUrl
     * @return SeasonDTO
     */
    public function setPosterUrl(string $posterUrl): SeasonDTO
    {
        $this->posterUrl = $posterUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosterUrl(): string
    {
        return $this->posterUrl;
    }
}
