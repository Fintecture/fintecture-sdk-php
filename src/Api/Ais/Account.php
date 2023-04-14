<?php

namespace Fintecture\Api\Ais;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Http;

class Account extends Api
{
    /**
     * Get information regarding the customer's account.
     *
     * @param string $customerId Customer Id.
     * @param string $accountId Account Id.
     * @param array $additionalParams Additional parameters.
     *     $params = [
     *         'remove_nulls' => (bool) true by default
     *         'withBalances' => (bool) true by default
     *     ]
     *
     * @return ApiResponse Customer's account information.
     */
    public function get(
        string $customerId,
        string $accountId = null,
        array $additionalParams = []
    ): ApiResponse {
        $path = '/ais/v1/customer/' . $customerId . '/accounts';
        if ($accountId) {
            $path .= '/' . $accountId;
        }
        if (!empty($additionalParams)) {
            $additionalParams = Http::buildHttpQuery($additionalParams);
            $path .= '?' . $additionalParams;
        }

        return $this->apiWrapper->get($path);
    }
}
