<?php

namespace App\MetadataAgents;

use App\MetadataAgents\Contracts\RatingInterface;
use Illuminate\Support\Carbon;

class ImdbMetadataAgent implements RatingInterface
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

    public function getMovie(string $imdbId, string $lang = 'en-US,en')
    {
        $this->config->language = $lang;
        $res = new \Imdb\Title($imdbId);

        return $this->formatMovie($res);
    }

    private function formatMovie(\Imdb\Title $title): array
    {
        return [
            'title' => $title->title(),
            'year' => $title->year(),
            'released' => $this->parseReleaseDate($title->releaseInfo()[0]),
            'runtime' => $title->runtime(),
            'tagline' => $title->tagline(),
            'summary' => $title->storyline(),
            'plot' => null, // $title->synopsis(),
            'poster_url' => $title->photo($thumb = false),
            'imdb_id' => $title->imdbid(),
            'imdb_rating' => $title->rating(),
            'imdb_votes' => $title->votes(),
        ];
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
