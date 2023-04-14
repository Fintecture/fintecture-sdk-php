<?php

namespace Fintecture\Api\Ais;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Http;

class AccountHolder extends Api
{
    /**
     * Get personal information of the customer.
     *
     * @param string $customerId Redirect URI.
     * @param array $additionalParams Additional parameters.
     *     $params = [
     *         'remove_nulls' => (bool) false by default
     *     ]
     *
     * @return ApiResponse Information of the customer.
     */
    public function get(string $customerId, array $additionalParams = []): ApiResponse
    {
        $path = '/ais/v1/customer/' . $customerId . '/accountholders';
        if (!empty($additionalParams)) {
            $additionalParams = Http::buildHttpQuery($additionalParams);
            $path .= '?' . $additionalParams;
        }

        return $this->apiWrapper->get($path);
    }
}
