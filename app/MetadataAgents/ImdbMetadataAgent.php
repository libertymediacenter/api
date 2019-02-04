<?php

namespace App\MetadataAgents;

use App\MetadataAgents\Contracts\MetadataAgentInterface;
use App\MetadataAgents\DTO\MovieDTO;
use App\MetadataAgents\DTO\ShowDTO;
use Illuminate\Support\Carbon;

class ImdbMetadataAgent implements MetadataAgentInterface
{
    private $config;

    public function __construct()
    {
        $this->config = new \Imdb\Config();
        $this->config->photodir = storage_path('app/public/assets/libraries/images');
        $this->config->cachedir = storage_path('app/cache');
    }

    /**
     * @param string $title
     * @param string|array $type
     * @return array
     */
    public function find(string $title, $type): array
    {
        $types = [$type];

        if (is_string($type)) {
            switch ($type) {
                case 'Movie':
                    $types[] = 'Short';
                    break;
                default:
                    break;
            }
        }

        $query = new \Imdb\TitleSearch($this->config);

        return $query->search($title, $types);
    }

    public function getMovie(string $imdbId, array $options = []): MovieDTO
    {
        $this->setConfig($options);

        $res = new \Imdb\Title($imdbId);

        return $this->formatMovie($res);
    }

    public function getShow(string $imdbId, array $options = []): ShowDTO
    {
        $this->setConfig($options);

        $res = new \Imdb\Title($imdbId);

        return $this->formatShow($res);
    }

    public function getSeason(string $imdbId, array $options = [])
    {
        $this->setConfig($options);
    }

    private function setConfig(array $config)
    {
        $this->config->language = $config['lang'] ?? 'en-US';
    }

    private function formatMovie(\Imdb\Title $result): MovieDTO
    {
        $movie = new MovieDTO();

        $movie->title = $result->title();
        $movie->year = $result->year();
        $movie->released = $this->parseReleaseDate($result->releaseInfo()[0]);
        $movie->runtime = $result->runtime();
        $movie->tagline = $result->tagline();
        $movie->summary = $result->storyline();
        $movie->posterUrl = $result->photo($thumb = false);
        $movie->imdbId = $result->imdbid();
        $movie->rating = $result->rating();
        $movie->votes = $result->votes();

        return $movie;
    }

    private function formatShow(\Imdb\Title $result): ShowDTO
    {
        $show = new ShowDTO();

        $show->title = $result->title();
        $show->posterUrl = $result->photo($thumb = false);
        $show->start_year = $result->year();
        $show->end_year = $result->endyear();
        $show->summary = $result->storyline();


        return $show;
    }

    private function parseReleaseDate(array $date)
    {
        array_key_exists('year', $date) ?: $date = $date[0];

        try {
            $str = implode('-', [$date['year'], $date['mon'], $date['day']]);

            return Carbon::parse($str);
        } catch (\Exception $e) {
            return null;
        }
    }
}
