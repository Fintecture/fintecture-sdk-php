<?php

namespace Fintecture\Api\Pis;

use Fintecture\Fintecture;
use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class RequestToPay extends Api
{
    /**
     * Initiate a request-to-pay.
     *
     * @param array $data Payload.
     * @param string $redirectUri Redirect URI.
     *
     * @return ApiResponse Generated request-to-pay.
     */
    public function generate(array $data, string $redirectUri = null): ApiResponse
    {
        $params = http_build_query([
            'redirect_uri' => $redirectUri
        ]);
        $path = '/pis/v2/request-to-pay?' . $params;

        return $this->apiWrapper->post($path, $data);
    }
}
