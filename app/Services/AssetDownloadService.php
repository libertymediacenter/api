<?php

namespace App\Services;

use GuzzleHttp\Client;

class AssetDownloadService
{
    /** @var \GuzzleHttp\Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $url
     * @param $model
     * @return string|null
     * @throws \ReflectionException
     */
    public function fetchImage(string $url, $model): ?string
    {
        try {
            $image = $this->client->get($url)->getBody();
        } catch (\Exception $exception) {
            return null;
        }

        $ext = \File::extension($url);
        $type = str_slug((new \ReflectionClass($model))->getShortName());
        $path = "assets/images/{$type}/{$model->id}.${ext}";

        \Storage::disk('local')->put("public/{$path}", $image);

        return $path;
    }
}
