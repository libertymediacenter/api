<?php

namespace App\MetadataAgents\DTO;

class ShowDTO
{
    /** @var string */
    public $title;
    /** @var string */
    public $posterUrl;
    /** @var int */
    public $start_year;
    /** @var int */
    public $end_year;
    /** @var string */
    public $summary;
    /** @var string */
    public $status;

    /**
     * @param string $title
     * @return ShowDTO
     */
    public function setTitle(string $title): ShowDTO
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $posterUrl
     * @return ShowDTO
     */
    public function setPosterUrl(string $posterUrl): ShowDTO
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

    /**
     * @param int $start_year
     * @return ShowDTO
     */
    public function setStartYear(int $start_year): ShowDTO
    {
        $this->start_year = $start_year;
        return $this;
    }

    /**
     * @return int
     */
    public function getStartYear(): int
    {
        return $this->start_year;
    }

    /**
     * @param int $end_year
     * @return ShowDTO
     */
    public function setEndYear(int $end_year): ShowDTO
    {
        $this->end_year = $end_year;
        return $this;
    }

    /**
     * @return int
     */
    public function getEndYear(): int
    {
        return $this->end_year;
    }

    /**
     * @param string $summary
     * @return ShowDTO
     */
    public function setSummary(string $summary): ShowDTO
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $status
     * @return ShowDTO
     */
    public function setStatus(string $status): ShowDTO
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }


}
