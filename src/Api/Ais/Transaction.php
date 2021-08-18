<?php

namespace Fintecture\Api\Ais;

use Fintecture\Fintecture;
use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Transaction extends Api
{
    /**
     * Get all transactions on the given account.
     *
     * @param string $customerId Customer Id.
     * @param string $accountId Account Id.
     * @param array $additionalParams Additional parameters.
     *     $params = [
     *         'remove_nulls' => (bool) false by default
     *         'convert_dates' => (bool) false by default
     *         'filter[date_to]' => (string) format: yyyy-mm-dd
     *         'filter[date_from]' => (string) format: yyyy-mm-dd
     *         'filter[date_from]=max' => (int)
     *     ]
     *
     * @return ApiResponse Account transactions.
     */
    public function get(string $customerId, string $accountId, array $additionalParams = []): ApiResponse
    {
        $path = '/ais/v1/customer/' . $customerId . '/accounts/' . $accountId . '/transactions';
        if (!empty($additionalParams)) {
            $additionalParams = http_build_query($additionalParams);
            $path .= '?' . $additionalParams;
        }

        return $this->apiWrapper->get($path);
    }
}
