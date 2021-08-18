<?php

namespace Fintecture\Api\Auth;

use Fintecture\Fintecture;
use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Token extends Api
{
    /**
     * Generate an Access Token.
     *
     * @param string $clientIdentifier ais or pis.
     *
     * @return ApiResponse Generated token.
     */
    public function generate(string $clientIdentifier = 'pis', string $code = null): ApiResponse
    {
        $appId = Fintecture::getConfig()->getAppId();

        $scope = $clientIdentifier === 'ais' ? 'AIS' : 'PIS';
        $grantType = $clientIdentifier === 'ais' ? 'authorization_code' : 'client_credentials';

        $body = array(
            'grant_type' => $grantType
        );
        if ($scope === 'AIS') {
            $body['code'] = $code;
        } else {
            $body['app_id'] = $appId;
        }
        $body['scope'] = $scope;

        return $this->apiWrapper->post('oauth/accesstoken', $body, false, null, 2);
    }

    /**
     * Refresh Access Token.
     *
     * @param string $refreshToken Refresh token obtained in generate method.
     *
     * @return ApiResponse Refreshed token.
     */
    public function refresh(string $refreshToken): ApiResponse
    {
        $body = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        );

        return $this->apiWrapper->post('oauth/refresh_token', $body, false, null, 2);
    }
}
