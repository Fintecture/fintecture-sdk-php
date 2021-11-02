<?php

namespace Fintecture\Api\Pis;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Initiate extends Api
{
    /**
     * Initiate a payment.
     *
     * @param array $data Payload.
     * @param string $providerId Provider Id.
     * @param string $redirectUri Redirect URI.
     * @param string $state State.
     *
     * @return ApiResponse Generated connect.
     */
    public function generate(array $data, string $providerId, string $redirectUri, string $state = null): ApiResponse
    {
        $params = http_build_query([
            'redirect_uri' => $redirectUri,
            'state' => $state
        ]);
        $path = '/pis/v2/provider/' . $providerId . '/initiate?' . $params;

        return $this->apiWrapper->post($path, $data);
    }
}
