<?php

namespace Mr\AventriSdk;


use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Mr\AventriSdk\Exception\AventriException;
use Mr\AventriSdk\Exception\InvalidCredentialsException;
use Mr\AventriSdk\Http\Client;
use Mr\AventriSdk\Http\Middleware\ErrorsMiddleware;
use Mr\AventriSdk\Service\RegistrationService;
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

    private static $instance;

    private $accountId;
    private $appId;
    private $appSecret;
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
    private function __construct($accountId, $appId, $appSecret, $token = null, array $options = [], array $httpOptions = [])
    {
        $this->accountId = $accountId;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->token = $token;
        $this->options = $options;

        $httpCommon = [
            "debug" => $this->options["debug"] ?? false
        ];

        $this->httpOptions = [
            'registration' => array_merge(
                [
                    'base_uri' => '<base_url>',
                    'headers' => $this->defaultHeaders
                ],
                $httpCommon,
                $httpOptions['account'] ?? []
            ),
        ];

        if ((!$accountId || !$appId || !$appSecret) && !$token) {
            throw new AventriException('Empty credentials');
        }

        if (!$token) {
            $this->authenticate();
        }

        // Create default handler with all the default middlewares
        $stack = HandlerStack::create();

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
                        'options' => array_merge($httpDefaultRuntimeOptions, $this->httpOptions['media'])
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
                        'repository' => \mr_srv_arg(UserRepository::class),
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
        $client = new Client($accountHttpOptions);
        $data = null;

        try {
            $data = $client->postData("<auth url>", [
                'username' => $this->appId,
                'password' => $this->appSecret
            ]);
        } catch (RequestException $ex) {
            // Just avoid request exception from propagating
            if ($this->isDebug()) {
                \mr_logger()->error($ex->getMessage());
            }
        }

        if (! isset($data, $data['data'], $data['data']['token'])) {
            throw new InvalidCredentialsException();
        }

        return $this->token = $data['data']['token'];
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

    protected static function create($accountId, $appId, $appSecret, $token, array $options, array $httpOptions)
    {
        self::$instance = new self($accountId, $appId, $appSecret, $token, $options, $httpOptions);
    }

    public static function setCredentials($accountId, $appId, $appSecret, array $options = [], array $httpOptions = [])
    {
        self::create($accountId, $appId, $appSecret, null, $options, $httpOptions);
    }

    public static function setAuthToken($token, array $options = [], array $httpOptions = [])
    {
        self::create(null,null, null, $token, $options, $httpOptions);
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

    protected function _getToken()
    {
        return $this->token;
    }

    /**
     * @return RegistrationService
     */
    protected function _getRegistrationService()
    {
        return $this->_get(RegistrationService::class);
    }
}
