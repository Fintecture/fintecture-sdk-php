<?php

namespace Fintecture\Api\Pis;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Header;

class RequestForPayout extends Api
{
    /**
     * Initiate a request-for-payout.
     *
     * @param array $data Payload.
     * @param string $redirectUri Redirect URI.
     * @param string $state State.
     * @param string $xCountry xCountry.
     * @param string $xLanguage xLanguage.
     *
     * @return ApiResponse Generated request-for-payout.
     */
    public function generate(
        array $data,
        string $redirectUri = null,
        string $state = null,
        string $xCountry = null,
        string $xLanguage = null
    ): ApiResponse {
        $params = http_build_query([
            'redirect_uri' => $redirectUri,
            'state' => $state
        ]);
        $path = '/pis/v2/request-for-payout';
        if ($params) {
            $path .= '?' . $params;
        }

        $headers = Header::generate('POST', $path, $data);
        $headers['x-country'] = $xCountry;
        $headers['x-language'] = $xLanguage;

        return $this->apiWrapper->post($path, $data, true, $headers);
    }
}
