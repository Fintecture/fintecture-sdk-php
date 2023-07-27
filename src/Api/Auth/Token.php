<?php

namespace Fintecture\Api\Auth;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Fintecture;
use Fintecture\Util\FintectureException;

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
        if (!$config = Fintecture::getConfig()) {
            throw new FintectureException('Token needs a configured client');
        }
        $appId = $config->getAppId();

        $scope = $clientIdentifier === 'ais' ? 'AIS' : 'PIS';
        $grantType = $clientIdentifier === 'ais' ? 'authorization_code' : 'client_credentials';

        $body = [
            'grant_type' => $grantType
        ];
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
        $body = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ];

        return $this->apiWrapper->post('oauth/refresh_token', $body, false, null, 2);
    }
}
