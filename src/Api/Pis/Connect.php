<?php

namespace Fintecture\Api\Pis;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Connect extends Api
{
    /**
     * Generate a Connect instance.
     *
     * @param array $data Payload.
     * @param string $state State.
     * @param string $redirectUri Redirect URI.
     * @param string $originUri Origin URI.
     *
     * @return ApiResponse Generated connect.
     */
    public function generate(
        array $data,
        string $state,
        string $redirectUri = null,
        string $originUri = null
    ): ApiResponse {
        $params = http_build_query([
            'state' => $state,
            'redirect_uri' => $redirectUri,
            'origin_uri' => $originUri
        ]);
        $path = '/pis/v2/connect?' . $params;
        return $this->apiWrapper->post($path, $data);
    }
}
