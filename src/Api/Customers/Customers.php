<?php

namespace Fintecture\Api\Customers;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Header;
use Fintecture\Util\Http;

class Customers extends Api
{
    /**
     * List all registered customers.
     *
     * @param string $customerId Customer Id.
     * @param array $additionalParams Additional parameters.
     *     $additionalParams = [
     *         'external_id' => (string)
     *         'page[number]' => (int) Defaults to 1
     *     ]
     *
     * @return ApiResponse
     */
    public function get(string $customerId = null, array $additionalParams = []): ApiResponse
    {
        $path = '/v1/customers';

        if ($customerId) {
            $path .= '/' . $customerId;
        }

        if (!empty($additionalParams)) {
            $additionalParams = Http::buildHttpQuery($additionalParams);
            $path .= '?' . $additionalParams;
        }
        return $this->apiWrapper->get($path);
    }

    /**
     * Create a customer.
     *
     * @param array $data Payload.
     *
     * @return ApiResponse
     */
    public function generate(array $data): ApiResponse
    {
        $path = '/v1/customers';

        $headers = Header::generate('POST', $path, $data);

        return $this->apiWrapper->post($path, $data, true, $headers);
    }
}
