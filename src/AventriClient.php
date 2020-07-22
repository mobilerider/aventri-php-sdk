<?php

namespace Mr\Aventri;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class AventriClient
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }
}
