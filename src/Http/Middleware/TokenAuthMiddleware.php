<?php

namespace Mr\AventriSdk\Http\Middleware;

use GuzzleHttp\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TokenAuthMiddleware
{
    const AUTH_HEADER = 'Authorization';

    protected $token;
    protected $eventId;

    public function __construct($token, $eventId = null)
    {
        $this->token = $token;
        $this->eventId = $eventId;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler)
    {
       
       
        return function (RequestInterface $request, array $options) use ($handler) {
            $queryString = $request->getUri()->getQuery();
            $queryParts = \GuzzleHttp\Psr7\parse_query($queryString);
            $queryParts['accesstoken'] = $this->token;
            $queryParts['eventid'] = $this->eventId;
            $queryString = \GuzzleHttp\Psr7\build_query($queryParts);
            $request = $request->withUri($request->getUri()->withQuery($queryString));
       

            /** @var Promise $promise */
            return $handler($request, $options);
        };
    }
}
