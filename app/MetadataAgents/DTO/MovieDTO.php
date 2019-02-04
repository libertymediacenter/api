<?php

namespace App\MetadataAgents\DTO;

class MovieDTO
{
    /** @var string */
    public $title;
    /** @var string */
    public $imdbId;
    /** @var int e.g 2000 */
    public $year;
    /** @var string */
    public $released;
    /** @var int seconds */
    public $runtime;
    /** @var string */
    public $tagline;
    /** @var string */
    public $summary;
    /** @var string url */
    public $posterUrl;
    /** @var float */
    public $rating;
    /** @var int */
    public $votes;

    /**
     * @param string $title
     * @return MovieDTO
     */
    public function setTitle(string $title): MovieDTO
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $imdbId
     * @return MovieDTO
     */
    public function setImdbId(string $imdbId): MovieDTO
    {
        $this->imdbId = $imdbId;
        return $this;
    }

    /**
     * @param int $year
     * @return MovieDTO
     */
    public function setYear(int $year): MovieDTO
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @param int $runtime
     * @return MovieDTO
     */
    public function setRuntime(int $runtime): MovieDTO
    {
        $this->runtime = $runtime;
        return $this;
    }

    /**
     * @param string $tagline
     * @return MovieDTO
     */
    public function setTagline(string $tagline): MovieDTO
    {
        $this->tagline = $tagline;
        return $this;
    }

    /**
     * @param string $summary
     * @return MovieDTO
     */
    public function setSummary(string $summary): MovieDTO
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @param string $posterUrl
     * @return MovieDTO
     */
    public function setPosterUrl(string $posterUrl): MovieDTO
    {
        $this->posterUrl = $posterUrl;
        return $this;
    }

    /**
     * @param mixed $rating
     * @return MovieDTO
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @param int $votes
     * @return MovieDTO
     */
    public function setVotes(int $votes): MovieDTO
    {
        $this->votes = $votes;
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
     * @return string
     */
    public function getImdbId(): string
    {
        return $this->imdbId;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @return int
     */
    public function getRuntime(): int
    {
        return $this->runtime;
    }

    /**
     * @return string
     */
    public function getTagline(): string
    {
        return $this->tagline;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getPosterUrl(): string
    {
        return $this->posterUrl;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return int
     */
    public function getVotes(): int
    {
        return $this->votes;
    }

    /**
     * @param string $released
     * @return MovieDTO
     */
    public function setReleased(string $released): MovieDTO
    {
        $this->released = $released;
        return $this;
    }

    /**
     * @return string
     */
    public function getReleased(): string
    {
        return $this->released;
    }
}
