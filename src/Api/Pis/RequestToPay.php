<?php

namespace Fintecture\Api\Pis;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Header;

class RequestToPay extends Api
{
    /**
     * Initiate a request-to-pay.
     *
     * @param array $data Payload.
     * @param string $xLanguage x-language.
     * @param string $redirectUri Redirect URI.
     *
     * @return ApiResponse Generated request-to-pay.
     */
    public function generate(
        array $data,
        string $xLanguage,
        string $redirectUri = null
    ): ApiResponse {
        $params = http_build_query([
            'redirect_uri' => $redirectUri
        ]);
        $path = '/pis/v2/request-to-pay';
        if ($params) {
            $path .= '?' . $params;
        }

        $headers = Header::generate('POST', $path, $data);
        $headers['x-language'] = $xLanguage;

        return $this->apiWrapper->post($path, $data, true, $headers);
    }
}
