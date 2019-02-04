<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;

    public function __construct(Repository $config)
    {
        parent::__construct($config);

        $this->config->get('proxy.proxies');

        $headerConfig = $this->config->get('proxy.forwarded_headers');
        if ($headerConfig) {
            switch ($headerConfig) {
                case 'aws':
                    $this->headers = Request::HEADER_X_FORWARDED_AWS_ELB;
                    break;
                default:
                    $this->headers = Request::HEADER_X_FORWARDED_ALL;
            }
        }
    }
}
