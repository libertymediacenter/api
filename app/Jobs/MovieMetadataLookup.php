<?php

namespace App\Jobs;

use App\MetadataAgents\ImdbMetadataAgent;
use App\MetadataAgents\TmdbMetadataAgent;
use App\Models\Genre;
use App\Models\Person;
use App\Models\Rating;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MovieMetadataLookup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Illuminate\Database\Eloquent\Model */
    private $model;
    private $type;

    private $logPrefix = self::class;
    /** @var ImdbMetadataAgent */
    private $imdbAgent;
    /** @var \App\MetadataAgents\TmdbMetadataAgent */
    private $tmdbMetadataAgent;

    /**
     * Create a new job instance.
     *
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->imdbAgent = resolve(ImdbMetadataAgent::class);
        $this->tmdbMetadataAgent = resolve(TmdbMetadataAgent::class);

        try {
            $this->type = (new \ReflectionClass($this->model))->getShortName();
        } catch (\ReflectionException $e) {
            Log::error("[$this->logPrefix]: Could not get model name.", [
                'line'    => $e->getLine(),
                'message' => $e->getMessage(),
                'trace'   => $e->getTrace(),
            ]);
        }

        $this->lookup();

        $this->model->save();

        $this->delete();
    }


    private function lookup(): void
    {
        $imdbResult = $this->lookupImdb();

        if ($imdbResult) {
            $this->model->fill((array)$imdbResult['movie']);

            $imdbResult['rating']->model_id = $this->model->id;
            $imdbResult['rating']->save();

            $this->model->poster = $this->fetchImage($imdbResult['movie']->posterUrl, $this->model, 'poster');
            $this->model->backdrop = $this->fetchImage($imdbResult['backdrop'], $this->model, 'backdrop');
            $this->model->save();

            $this->attachGenres($imdbResult['movie']->genres);
            $this->attachCast($imdbResult['movie']->cast);
        }
    }

    /**
     * @param \Illuminate\Support\Collection $genres
     */
    private function attachGenres(Collection $genres)
    {
        $genres->each(function (string $name) {
            $genre = Genre::firstOrCreate(['name' => $name]);

            $this->model->genres()->attach($genre);
        });
    }

    /**
     * @param \Illuminate\Support\Collection $cast
     */
    private function attachCast(Collection $cast)
    {
        $cast->each(function (array $personData) {
            try {
                $person = Person::whereImdbId('tt' . $personData['imdb'])->firstOrFail([]);
            } catch (ModelNotFoundException $exception) {
                $person = Person::create([
                    'name'    => $personData['name'],
                    'imdb_id' => 'tt' . $personData['imdb'],
                ]);

                if ($personData['photo']) {
                    $person->photo = $this->fetchImage($personData['photo'], $person, 'person');
                    $person->save();
                }
            }

            if ($person->id) {
                $this->model->cast()->attach($person, ['role' => $personData['role']]);
            }
        });
    }

    /**
     * @return array|null
     */
    private function lookupImdb(): ?array
    {
        /** @var \Imdb\Title $result */
        $result = $this->imdbAgent->find($this->model->title, $this->type);

        if (!$result) {
            return null;
        }

        $result = $result[0];

        switch ($this->type) {
            case 'Movie':
                $data = $this->imdbAgent->getMovie($result->imdbid());
                $imdbId = 'tt' . $result->imdbid();

                $backdrop = $this->tmdbMetadataAgent->getBackdropByImdbId($imdbId);

                return [
                    'movie'    => $data,
                    'backdrop' => $backdrop,
                    'rating'   => Rating::make([
                        'model_type'  => Rating::class,
                        'provider_id' => $imdbId,
                        'provider'    => Rating::IMDB,
                        'score'       => $data->rating,
                        'votes'       => $data->votes,
                    ]),
                ];
            case 'Show':
                break;
        }
    }

    private function fetchImage(string $url, $model, string $imageType)
    {
        $guzzle = new Client();

        try {
            $r = $guzzle->get($url);
            $image = $r->getBody();
        } catch (\Exception $exception) {
            return;
        }

        try {
            $ext = File::extension($url);
            $type = str_slug((new \ReflectionClass($model))->getShortName());
            $path = "assets/images/{$type}/{$model->id}-{$imageType}.${ext}";

        } catch (\Exception $exception) {
            return;
        }

        \Storage::disk('local')->put("public/{$path}", $image);

        return $path;
    }
}
