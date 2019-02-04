<?php

namespace App\Jobs;

use App\MetadataAgents\ImdbMetadataAgent;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Rating;
use App\Models\Show;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

class MetadataLookup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Illuminate\Database\Eloquent\Model */
    private $model;
    private $type;

    private $logPrefix = self::class;

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
    public function handle()
    {
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
        $genres = collect([]);

        if ($imdbResult) {
            $this->model->fill((array)$imdbResult['movie']);

            /** @var \Illuminate\Support\Collection $genres */
            $genres->push(...$imdbResult['movie']->genres);

            $imdbResult['rating']->model_id = $this->model->id;
            $imdbResult['rating']->save();

            $this->fetchPoster($imdbResult['movie']->posterUrl);
        }

        $genres->each(function (string $name) {
            $genre = Genre::firstOrCreate(['name' => $name]);

            $this->model->genres()->attach($genre);
        });
    }

    private function lookupImdb()
    {
        $imdbAgent = new ImdbMetadataAgent();
        /** @var \Imdb\Title $result */
        $result = $imdbAgent->find($this->model->title, $this->type);

        if (!$result) {
            return null;
        }

        $result = $result[0];

        switch ($this->type) {
            case 'Movie':
                $data = $imdbAgent->getMovie($result->imdbid());

                return [
                    'movie'  => $data,
                    'rating' => Rating::make([
                        'model_type'  => Rating::class,
                        'provider_id' => 'tt' . $result->imdbid(),
                        'provider'    => Rating::IMDB,
                        'score'       => $data->rating,
                        'votes'       => $data->votes,
                    ]),
                ];
            case 'Show':
                break;
        }
    }

    private function fetchPoster(string $url)
    {
        $guzzle = new Client();

        try {
            $r = $guzzle->get($url);
            $image = $r->getBody();
        } catch (\Exception $exception) {
            return;
        }

        $ext = File::extension($url);
        $path = "assets/libraries/images/{$this->model->id}.${ext}";

        \Storage::disk('local')->put("public/{$path}", $image);

        $this->model->poster = $path;
    }
}
