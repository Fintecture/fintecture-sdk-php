<?php

namespace Fintecture\Api\Ais;

use Fintecture\Fintecture;
use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Api\Auth\Token;
use Fintecture\Util\Header;

class Authorize extends Api
{
    /**
     * Generate authentication instance.
     *
     * @param string $providerId Provider Id.
     * @param string $redirectUri Redirect URI.
     * @param bool $useToken Use token auth.
     *
     * @return ApiResponse Generated authentication.
     */
    public function generate(string $providerId, string $redirectUri, bool $useToken = true): ApiResponse
    {
        $params = [
            'redirect_uri' => $redirectUri,
        ];
        if (!$useToken) {
            $params['response_type'] = 'code';
        }
        $params = http_build_query($params);
        $path = '/ais/v1/provider/' . $providerId . '/authorize?' . $params;

        if (!$useToken) {
            return $this->apiWrapper->get($path, null, 0);
        } else {
            return $this->apiWrapper->get($path, null, 1);
        }
    }

    /**
     * Generate decoupled authentication instance.
     *
     * @param string $providerId Provider Id.
     * @param string $pollingId Polling Id.
     * @param string $xPsuId x-psu-id.
     * @param string $xPsuIpAddress x-psu-ip-address.
     * @param bool $useToken Use token auth.
     *
     * @return ApiResponse Generated authentication.
     */
    public function generateDecoupled(
        string $providerId,
        string $pollingId,
        string $xPsuId,
        string $xPsuIpAddress,
        bool $useToken = true
    ): ApiResponse {
        $path = '/ais/v1/provider/' . $providerId . '/authorize/decoupled/' . $pollingId;
        if (!$useToken) {
            $params = [
                'response_type' => 'code'
            ];
            $params = http_build_query($params);
            $path .= '?' . $params;
        }

        $authMethod = $useToken ? 1 : 0;
        $headers = Header::generate('GET', $path, null, $authMethod);
        $headers['x-psu-id'] = $xPsuId;
        $headers['x-psu-ip-address'] = $xPsuIpAddress;

        return $this->apiWrapper->get($path, $headers, $authMethod);
    }
}
