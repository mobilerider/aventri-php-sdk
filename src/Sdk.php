<?php

namespace Mr\AventriSdk;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Mr\AventriSdk\Exception\AventriException;
use Mr\AventriSdk\Exception\InvalidCredentialsException;
use Mr\AventriSdk\Http\Client;
use Mr\AventriSdk\Http\Middleware\ErrorsMiddleware;
use Mr\AventriSdk\Http\Middleware\TokenAuthMiddleware;
use Mr\AventriSdk\Repository\Registration\AttendeeRepository;
use Mr\AventriSdk\Service\RegistrationService;
use Mr\AventriSdk\Model\Registration\Attendee;
use Mr\Bootstrap\Container;
use Mr\Bootstrap\Interfaces\ContainerAccessorInterface;
use Mr\Bootstrap\Traits\ContainerAccessor;
use Mr\Bootstrap\Utils\Logger;

/**
 * @method static RegistrationService getRegistrationService
 * @method static string getAccountId
 *
 * Class Sdk
 * @package Mr\AventriSdk
 */
class Sdk implements ContainerAccessorInterface
{
    use ContainerAccessor;

    const BASE_URL = 'https://api-na.eventscloud.com/api/';

    const API_VERSION = 'v2/';

    private static $instance;
    private $accountId;
    private $appId;
    private $eventId;
    private $token;
    private $options;
    private $httpOptions;

    private $defaultHeaders = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];

    /**
     * Service constructor.
     * @param $accountId
     * @param $appId
     * @param $appSecret
     * @param string $token
     * @param array $options
     * @param array $httpOptions
     * @throws MrException
     */
    private function __construct($accountId, $appId, $eventId = null, $token = null, array $options = [], array $httpOptions = [])
    {
        $this->accountId = $accountId;
        $this->appId = $appId;
        $this->eventId = $eventId;
        $this->token = $token;
        $this->options = $options;

        $httpCommon = [
            "debug" => $this->options["debug"] ?? false
        ];

        $this->httpOptions = [
            'registration' => array_merge(
                [
                    'base_uri' => static::BASE_URL,
                    'headers' => $this->defaultHeaders
                ],
                $httpCommon,
                $httpOptions['registration'] ?? []
            ),
        ];

        if ((!$accountId || !$appId ) && !$token) {
            throw new AventriException('Empty credentials');
        }

        if (!$token) {
             $token = $this->authenticate();
        }

        // Create default handler with all the default middlewares
        $stack = HandlerStack::create();
        $stack->remove('http_errors');
        $stack->unshift(new TokenAuthMiddleware($token, $eventId), 'auth');

        // Last to un-shift so it remains first to execute
        $stack->unshift(new ErrorsMiddleware([]), 'http_errors');
        $httpDefaultRuntimeOptions = [
            'handler' => $stack,
        ];

        $customDefinitions = isset($options['definitions']) ? $options['definitions'] : [];

        $definitions = $customDefinitions + [
                'Logger' => [
                    'single' => true,
                    'instance' => Logger::getInstance(),
                ],
                // Clients
                'RegistrationClient' => [
                    'single' => true,
                    'class' => Client::class,
                    'arguments' => [
                        'options' => array_merge($httpDefaultRuntimeOptions, $this->httpOptions['registration'])
                    ]
                ],
                // Services
                RegistrationService::class => [
                    'single' => true,
                    'class' => RegistrationService::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('RegistrationClient')
                    ]
                ],
                // Repositories
                AttendeeRepository::class => [
                    'single' => true,
                    'class' => AttendeeRepository::class,
                    'arguments' => [
                        'client' => \mr_srv_arg('RegistrationClient'),
                        'options' => []
                    ]
                ],
                // Models
                Attendee::class => [
                    'single' => false,
                    'class' => Attendee::class,
                    'arguments' => [
                        'repository' => \mr_srv_arg(AttendeeRepository::class),
                        'data' => null
                    ]
                ],
            ];

        $this->container = new Container($definitions);
    }

    protected function isDebug()
    {
        return $this->options['debug'] ?? false;
    }

    protected function authenticate()
    {
        $client = new Client($this->httpOptions);
        $data = null;

        try {
            $data = $client->getData(static::BASE_URL . static::API_VERSION . 'global/authorize.json', [
                'accountid' => $this->accountId,
                'key' => $this->appId
            ]);
        } catch (RequestException $ex) {
            // Just avoid request exception from propagating
            if ($this->isDebug()) {
                \mr_logger()->error($ex->getMessage());
            }
        }

        if (! isset($data, $data['accesstoken'])) {
            throw new InvalidCredentialsException();
        }
      
        return $data['accesstoken'];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function getHttpOptions()
    {
        return $this->httpOptions;
    }

    protected static function create($accountId, $appId, $eventId, $token, array $options, array $httpOptions)
    {
        self::$instance = new self($accountId, $appId, $eventId, $token, $options, $httpOptions);
    }

    public static function setCredentials($accountId, $appId, $eventId = null, array $options = [], array $httpOptions = [])
    {
        self::create($accountId, $appId, $eventId, null, $options, $httpOptions);
    }

    public static function setAuthToken($token, array $options = [], array $httpOptions = [])
    {
        self::create(null, null, null, $token, $options, $httpOptions);
    }

    /**
     * @return Sdk
     */
    protected static function getInstance()
    {
        if (!self::$instance) {
            throw new \RuntimeException('You need to set credentials or auth token first');
        }

        return self::$instance;
    }

    public static function isAuthenticated()
    {
        return (bool) self::$instance;
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = self::getInstance();

        $name = '_' . $name;

        return call_user_func_array([$instance, $name], $arguments);
    }

    protected function _getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return token
     */
    protected function _getToken()
    {
        return $this->token;
    }

    /**
     * @return eventId
     */
    protected function _getEventId()
    {
        return $this->eventId;
    }

    /**
     * @return RegistrationService
     */
    protected function _getRegistrationService()
    {
        return $this->_get(RegistrationService::class);
    }
}
