<?php

namespace Fintecture;

use Fintecture\Api\ApiResponse;
use Fintecture\Api\ApiWrapper;
use Fintecture\Config\Config;
use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class Fintecture
{
    // SDK Version
    public const VERSION = '2.3.5';

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
     * @var array<ClientInterface>
     */
    private static $httpClients;

    /**
     * @var array<RequestFactoryInterface>
     */
    private static $requestFactories;

    /**
     * @var array<StreamFactoryInterface>
     */
    private static $streamFactories;

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
    public static function setCurrentClient(string $currentClient): void
    {
        self::$currentClient = $currentClient;
    }

    /**
     * Get default HTTP client.
     *
     * @return ClientInterface Default HTTP client
     */
    public static function getDefaultHttpClient(): ?ClientInterface
    {
        try {
            return Psr18ClientDiscovery::find();
        } catch (NotFoundException $e) {
            \trigger_error($e->getMessage(), E_USER_WARNING);
            return null;
        }
    }

    /**
     * Get default Request Factory.
     *
     * @return RequestFactoryInterface Default Request Factory
     */
    public static function getDefaultRequestFactory(): ?RequestFactoryInterface
    {
        try {
            return Psr17FactoryDiscovery::findRequestFactory();
        } catch (NotFoundException $e) {
            \trigger_error($e->getMessage(), E_USER_WARNING);
            return null;
        }
    }

    /**
     * Get default Request Factory.
     *
     * @return StreamFactoryInterface Default Stream Factory
     */
    public static function getDefaultStreamFactory(): ?StreamFactoryInterface
    {
        try {
            return Psr17FactoryDiscovery::findStreamFactory();
        } catch (NotFoundException $e) {
            \trigger_error($e->getMessage(), E_USER_WARNING);
            return null;
        }
    }

    /**
     * Get current HTTP client.
     *
     * @return ClientInterface Current HTTP client
     */
    public static function getHttpClient(): ?ClientInterface
    {
        if (!empty(self::getCurrentClient()) && isset(self::$httpClients[self::getCurrentClient()])) {
            return self::$httpClients[self::getCurrentClient()];
        }
        return self::getDefaultHttpClient();
    }

    /**
     * Set current HTTP client.
     *
     * @param ClientInterface $httpClient Current HTTP client
     */
    public static function setHttpClient(?ClientInterface $httpClient): void
    {
        if (!self::$httpClients) {
            self::$httpClients = [];
        }
        self::$httpClients[self::getCurrentClient()] = $httpClient ?: self::getDefaultHttpClient();
    }

    /**
     * Get current Request Factory.
     *
     * @return RequestFactoryInterface Current Request Factory
     */
    public static function getRequestFactory(): ?RequestFactoryInterface
    {
        if (!empty(self::getCurrentClient()) && isset(self::$requestFactories[self::getCurrentClient()])) {
            return self::$requestFactories[self::getCurrentClient()];
        }
        return self::getDefaultRequestFactory();
    }

    /**
     * Set current Request Factory.
     */
    public static function setRequestFactory(): void
    {
        if (!self::$requestFactories) {
            self::$requestFactories = [];
        }
        self::$requestFactories[self::getCurrentClient()] = self::getDefaultRequestFactory();
    }

    /**
     * Get current Stream Factory.
     *
     * @return StreamFactoryInterface Current Stream Factory
     */
    public static function getStreamFactory(): ?StreamFactoryInterface
    {
        if (!empty(self::getCurrentClient()) && isset(self::$streamFactories[self::getCurrentClient()])) {
            return self::$streamFactories[self::getCurrentClient()];
        }
        return self::getDefaultStreamFactory();
    }

    /**
     * Set current Stream Factory.
     */
    public static function setStreamFactory(): void
    {
        if (!self::$streamFactories) {
            self::$streamFactories = [];
        }
        self::$streamFactories[self::getCurrentClient()] = self::getDefaultStreamFactory();
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
    public static function setConfig(array $config): void
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
    public static function setAccessToken(ApiResponse $accessToken): void
    {
        self::$accessTokens[self::getCurrentClient()] = $accessToken;
    }
}
