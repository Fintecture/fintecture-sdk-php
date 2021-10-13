<?php

namespace Fintecture;

use Fintecture\Api\ApiResponse;
use Fintecture\Api\ApiWrapper;
use Fintecture\Config\Config;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;

final class Fintecture
{
    // SDK Version
    public const VERSION = '2.0.6';

    // API URLs

    // Main
    public const TEST_API_URL = 'https://api-sandbox-test.fintecture.com/';
    public const SANDBOX_API_URL = 'https://api-sandbox.fintecture.com/';
    public const PRODUCTION_API_URL = 'https://api.fintecture.com/';

    // Environment
    public const DEFAULT_ENV = 'sandbox';
    public const AVAILABLE_ENVS = ['test', 'sandbox', 'production'];

    /**
     * @var string
     */
    public static $currentClient;

    /**
     * @var array<HttpClient>
     */
    private static $httpClients;

    /**
     * @var array<MessageFactory>
     */
    private static $messageFactories;

    /**
     * @var ApiWrapper
     */
    private static $apiWrapper;

    /**
     * @var array<Config>
     */
    public static $configs;

    /**
     * @var array<ApiResponse>
     */
    public static $accessTokens;

    /**
     * Get current client identifier.
     *
     * @return string Current client identifier
     */
    public static function getCurrentClient(): ?string
    {
        return self::$currentClient;
    }

    /**
     * Set current client identifier.
     *
     * @param string $currentClient Current client identifier
     */
    public static function setCurrentClient(string $currentClient)
    {
        self::$currentClient = $currentClient;
    }

    /**
     * Get default HTTP client.
     *
     * @return HttpClient Default HTTP client
     */
    public static function getDefaultHttpClient(): ?HttpClient
    {
        return HttpClientDiscovery::find();
    }

    /**
     * Get default Message Factory.
     *
     * @return MessageFactory Default Message Factory
     */
    public static function getDefaultMessageFactory(): ?MessageFactory
    {
        return MessageFactoryDiscovery::find();
    }

    /**
     * Get current HTTP client.
     *
     * @return HttpClient Current HTTP client
     */
    public static function getHttpClient(): ?HttpClient
    {
        if (!empty(self::getCurrentClient()) && isset(self::$httpClients[self::getCurrentClient()])) {
            return self::$httpClients[self::getCurrentClient()];
        }
        return self::getDefaultHttpClient();
    }

    /**
     * Set current HTTP client.
     *
     * @param HttpClient $httpClient Current HTTP client
     */
    public static function setHttpClient(?HttpClient $httpClient)
    {
        if (!self::$httpClients) {
            self::$httpClients = [];
        }
        self::$httpClients[self::getCurrentClient()] = $httpClient ?: self::getDefaultHttpClient();
    }

    /**
     * Get current Message Factory.
     *
     * @return MessageFactory Current Message Factory
     */
    public static function getMessageFactory(): ?MessageFactory
    {
        if (!empty(self::getCurrentClient()) && isset(self::$messageFactories[self::getCurrentClient()])) {
            return self::$messageFactories[self::getCurrentClient()];
        }
        return self::getDefaultMessageFactory();
    }

    /**
     * Set current Message Factory.
     */
    public static function setMessageFactory()
    {
        if (!self::$messageFactories) {
            self::$messageFactories = [];
        }
        self::$messageFactories[self::getCurrentClient()] = self::getDefaultMessageFactory();
    }

    /**
     * Get Api Wrapper.
     *
     * @return ApiWrapper Api Wrapper
     */
    public static function getApiWrapper(): ?ApiWrapper
    {
        return self::$apiWrapper;
    }

    /**
     * Set Api Wrapper.
     */
    public static function setApiWrapper(): ApiWrapper
    {
        self::$apiWrapper = new ApiWrapper();
        return self::$apiWrapper;
    }

    /**
     * Get current Config.
     *
     * @return Config current Config
     */
    public static function getConfig(): ?Config
    {
        if (!empty(self::getCurrentClient()) && isset(self::$configs[self::getCurrentClient()])) {
            return self::$configs[self::getCurrentClient()];
        }
        return null;
    }

    /**
     * Set current Config.
     *
     * @param array $config current Config
     */
    public static function setConfig(array $config)
    {
        if (!self::$configs) {
            self::$configs = [];
        }
        self::$configs[self::getCurrentClient()] = new Config($config);
        self::$configs[self::getCurrentClient()]->validate();
    }

    /**
     * Get current Access Token.
     *
     * @return ApiResponse current Access Token
     */
    public static function getAccessToken(): ?ApiResponse
    {
        if (!empty(self::getCurrentClient()) && isset(self::$accessTokens[self::getCurrentClient()])) {
            return self::$accessTokens[self::getCurrentClient()];
        }
        return null;
    }

    /**
     * Set current Access Token.
     *
     * @param ApiResponse $accessToken current Access Token
     */
    public static function setAccessToken(ApiResponse $accessToken)
    {
        self::$accessTokens[self::getCurrentClient()] = $accessToken;
    }
}
