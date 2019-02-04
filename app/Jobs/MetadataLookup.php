<?php

namespace App\Jobs;

use App\MetadataAgents\ImdbMetadataAgent;
use App\Models\Episode;
use App\Models\Movie;
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

    private $logPrefix = '';

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
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
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
            $this->model->fill($imdbResult);
            $this->fetchPoster($imdbResult['poster_url']);
        }
    }

    private function lookupImdb(): ?array
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
                return $imdbAgent->getMovie($result->imdbid());
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

    private function parseType()
    {
        if ($this->model instanceof Movie) {
            return 'Movie';
        }

        if ($this->model instanceof Show) {
            return 'Show';
        }

        if ($this->model instanceof Episode) {
            return 'Episode';
        }

        throw new \RuntimeException('Unknown type');
    }
}
