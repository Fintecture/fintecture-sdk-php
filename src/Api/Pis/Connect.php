<?php

namespace Fintecture\Api\Pis;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Header;
use Fintecture\Util\Http;

class Connect extends Api
{
    /**
     * Generate a Connect instance.
     *
     * @param array $data Payload.
     * @param string $state State.
     * @param string $redirectUri Redirect URI.
     * @param string $originUri Origin URI.
     * @param bool $withBeneficiary With beneficiary.
     * @param bool $withVirtualBeneficiary With virtual beneficiary.
     * @param array $additionalHeaders additionalHeaders.
     *     $additionalHeaders = [
     *         'x-country' => (string)
     *         'x-language' => (string)
     *         'x-provider' => (string)
     *         'x-psu-type' => (string)
     *     ]
     *
     * @return ApiResponse Generated connect.
     */
    public function generate(
        array $data,
        string $state,
        string $redirectUri = null,
        string $originUri = null,
        bool $withBeneficiary = null,
        bool $withVirtualBeneficiary = null,
        array $additionalHeaders = []
    ): ApiResponse {
        $params = Http::buildHttpQuery([
            'state' => $state,
            'redirect_uri' => $redirectUri,
            'origin_uri' => $originUri,
            'with_beneficiary' => $withBeneficiary,
            'with_virtualbeneficiary' => $withVirtualBeneficiary
        ]);
        $path = '/pis/v2/connect?' . $params;

        $headers = Header::generate('POST', $path, $data);
        if (!empty($additionalHeaders)) {
            $headers = array_merge($headers, $additionalHeaders);
        }

        return $this->apiWrapper->post($path, $data, true, $headers);
    }
}
