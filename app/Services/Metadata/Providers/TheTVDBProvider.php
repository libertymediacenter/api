<?php

namespace App\Services\Metadata\Providers;

use Illuminate\Support\Facades\Redis;

class TheTVDBProvider
{
    /** @var \Adrenth\Thetvdb\Client */
    private $client;
    private $authenticated = false;

    public function __construct()
    {
        $client = new \Adrenth\Thetvdb\Client();

        $this->client = $client;
    }

    public function getClient()
    {
        if (!$this->authenticated) {
            $this->authenticate();
        }

        return $this->client;
    }

    /**
     * @throws \Adrenth\Thetvdb\Exception\UnauthorizedException
     */
    private function authenticate()
    {
        $token = Redis::get('metadata:provider:tvdb:token');

        if ($token === null) {
            $token = $this->client
                ->authentication()
                ->login(
                    config('metadata.providers.tvdb.key'),
                    config('metadata.providers.tvdb.username'),
                    config('metadata.providers.tvdb.identifier')
                );
        }

        Redis::set('metadata:provider:tvdb:token', $token);

        $this->client->setToken($token);

        $this->authenticated = true;
    }
}
