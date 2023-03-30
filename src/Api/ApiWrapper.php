<?php

namespace Fintecture\Api;

use Fintecture\Config\Endpoint;
use Fintecture\Fintecture;
use Fintecture\Util\Crypto;
use Fintecture\Util\Header;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ApiWrapper
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    public function __construct()
    {
        $this->httpClient = Fintecture::getHttpClient();
        $this->requestFactory = Fintecture::getRequestFactory();
        $this->streamFactory = Fintecture::getStreamFactory();
    }

    /**
     * GET query to the API.
     *
     * @param string $endpoint Endpoint
     * @param array $headers Headers provided to the request
     * @param int $authMethod Auth method: 0 => App Id, 1 => Token, 2 => Basic Auth
     *
     * @return ApiResponse $result response of the query
     */
    public function get(
        string $endpoint,
        array $headers = null,
        int $authMethod = 1
    ): ApiResponse {
        if (!$headers) {
            try {
                $headers = Header::generate('GET', $endpoint, null, $authMethod);
            } catch (\Exception $e) {
                \trigger_error($e->getMessage(), E_USER_ERROR);
            }
        }

        try {
            $request = $this->requestFactory->createRequest('GET', $this->getFinalURL($endpoint));
            if (!empty($headers)) {
                $request = $this->addHeadersToRequest($request, $headers);
            }

            $response = $this->httpClient->sendRequest($request);
        } catch (\Exception $e) {
            throw new \Exception("Can't handle HTTP request: " . $e->getMessage());
        }

        $result = json_decode($response->getBody()->getContents());
        return new ApiResponse($response, $result);
    }

    /**
     * POST query to the API.
     *
     * @param string $endpoint Endpoint
     * @param mixed $body Body fields provided to the request
     * @param bool $json Whether the body need to be JSON encoded or not
     * @param array $headers Headers provided to the request
     * @param int $authMethod Auth method: 0 => App Id, 1 => Token, 2 => Basic Auth
     *
     * @return ApiResponse $result response of the query
     */
    public function post(
        string $endpoint,
        $body = [],
        bool $json = true,
        array $headers = null,
        int $authMethod = 1
    ): ApiResponse {
        if (!$headers) {
            try {
                $headers = Header::generate('POST', $endpoint, $body, $authMethod);
            } catch (\Exception $e) {
                \trigger_error($e->getMessage(), E_USER_ERROR);
            }
        }

        try {
            $request = $this->requestFactory->createRequest('POST', $this->getFinalURL($endpoint));
            if (!empty($headers)) {
                $request = $this->addHeadersToRequest($request, $headers);
            }

            if (!empty($body) && is_array($body)) {
                $body = $json ? Crypto::encodeToJson($body) : http_build_query($body);
                $request = $this->addBodyToRequest($request, $body);
            }

            $response = $this->httpClient->sendRequest($request);
        } catch (\Exception $e) {
            throw new \Exception("Can't handle HTTP request: " . $e->getMessage());
        }

        $result = json_decode($response->getBody()->getContents());
        return new ApiResponse($response, $result);
    }

    /**
     * PATCH query to the API.
     *
     * @param string $endpoint Endpoint
     * @param mixed $body Body fields provided to the request
     * @param bool $json Whether the body need to be JSON encoded or not
     * @param array $headers Headers provided to the request
     * @param int $authMethod Auth method: 0 => App Id, 1 => Token, 2 => Basic Auth
     *
     * @return ApiResponse $result response of the query
     */
    public function patch(
        string $endpoint,
        $body = null,
        bool $json = true,
        array $headers = null,
        int $authMethod = 1
    ): ApiResponse {
        if (!$headers) {
            try {
                $headers = Header::generate('PATCH', $endpoint, $body, $authMethod);
            } catch (\Exception $e) {
                \trigger_error($e->getMessage(), E_USER_ERROR);
            }
        }

        try {
            $request = $this->requestFactory->createRequest('PATCH', $this->getFinalURL($endpoint));
            if (!empty($headers)) {
                $request = $this->addHeadersToRequest($request, $headers);
            }

            if (!empty($body) && is_array($body)) {
                $body = $json ? Crypto::encodeToJson($body) : http_build_query($body);
                $request = $this->addBodyToRequest($request, $body);
            }

            $response = $this->httpClient->sendRequest($request);
        } catch (\Exception $e) {
            throw new \Exception("Can't handle HTTP request: " . $e->getMessage());
        }

        $result = json_decode($response->getBody()->getContents());
        return new ApiResponse($response, $result);
    }

    /**
     * DELETE query to the API.
     *
     * @param string $endpoint Endpoint
     * @param mixed $body Body fields provided to the request
     * @param bool $json Whether the body need to be JSON encoded or not
     * @param array $headers Headers provided to the request
     * @param int $authMethod Auth method: 0 => App Id, 1 => Token, 2 => Basic Auth
     *
     * @return ApiResponse $result response of the query
     */
    public function delete(
        string $endpoint,
        $body = null,
        bool $json = true,
        array $headers = null,
        int $authMethod = 1
    ): ApiResponse {
        if (!$headers) {
            try {
                $headers = Header::generate('DELETE', $endpoint, $body, $authMethod);
            } catch (\Exception $e) {
                \trigger_error($e->getMessage(), E_USER_ERROR);
            }
        }

        try {
            $request = $this->requestFactory->createRequest('DELETE', $this->getFinalURL($endpoint));
            if (!empty($headers)) {
                $request = $this->addHeadersToRequest($request, $headers);
            }

            if (!empty($body) && is_array($body)) {
                $body = $json ? Crypto::encodeToJson($body) : http_build_query($body);
                $request = $this->addBodyToRequest($request, $body);
            }

            $response = $this->httpClient->sendRequest($request);
        } catch (\Exception $e) {
            throw new \Exception("Can't handle HTTP request: " . $e->getMessage());
        }

        $result = json_decode($response->getBody()->getContents());
        return new ApiResponse($response, $result);
    }

    private function addHeadersToRequest(RequestInterface $request, array $headers): RequestInterface
    {
        foreach ($headers as $headerKey => $headerValue) {
            $request = $request->withHeader($headerKey, $headerValue);
        }

        return $request;
    }

    private function addBodyToRequest(RequestInterface $request, string $body): RequestInterface
    {
        $stream = $this->streamFactory->createStream($body);
        $request = $request->withBody($stream);

        return $request;
    }

    private function getFinalURL(string $endpoint): string
    {
        if (substr($endpoint, 0, 4) === 'http') {
            $url = $endpoint;
        } else {
            $url = rtrim(Endpoint::getApiUrl(), '/') . '/' . ltrim($endpoint, '/');
        }
        return $url;
    }
}
