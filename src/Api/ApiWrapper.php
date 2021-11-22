<?php

namespace Fintecture\Api;

use Fintecture\Config\Endpoint;
use Fintecture\Fintecture;
use Fintecture\Util\Crypto;
use Fintecture\Util\Header;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;

class ApiWrapper
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct()
    {
        $this->httpClient = Fintecture::getHttpClient();
        $this->messageFactory = Fintecture::getMessageFactory();
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
            $response = $this->httpClient->sendRequest(
                $this->messageFactory->createRequest('GET', $this->getFinalURL($endpoint), $headers)
            );
        } catch (\Exception $e) {
            throw new \Exception('Can\'t handle HTTP request');
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

        if (!empty($body) && is_array($body)) {
            if ($json) {
                $body = Crypto::encodeToJson($body);
            } else {
                $body = http_build_query($body);
            }
        }

        try {
            $response = $this->httpClient->sendRequest(
                $this->messageFactory->createRequest('POST', $this->getFinalURL($endpoint), $headers, $body)
            );
        } catch (\Exception $e) {
            throw new \Exception('Can\'t handle HTTP request');
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

        if ($body && is_array($body)) {
            if ($json) {
                $body = Crypto::encodeToJson($body);
            } else {
                $body = http_build_query($body);
            }
        }

        try {
            $response = $this->httpClient->sendRequest(
                $this->messageFactory->createRequest('DELETE', $this->getFinalURL($endpoint), $headers, $body)
            );
        } catch (\Exception $e) {
            throw new \Exception('Can\'t handle HTTP request');
        }

        $result = json_decode($response->getBody()->getContents());
        return new ApiResponse($response, $result);
    }

    private function getFinalURL($endpoint): string
    {
        if (substr($endpoint, 0, 4) === 'http') {
            $url = $endpoint;
        } else {
            $url = rtrim(Endpoint::getApiUrl(), '/') . '/' . ltrim($endpoint, '/');
        }
        return $url;
    }
}
