<?php

namespace Fintecture\Api\Customers;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Header;
use Fintecture\Util\Http;

class CustomerBankAccount extends Api
{
    /**
     * List all customer bank accounts.
     *
     * @param string $customerId Customer Id.
     * @param string $bankAccountId Bank Account Id.
     * @param array $additionalParams Additional parameters.
     *     $additionalParams = [
     *         'page[number]' => (int) Defaults to 1
     *     ]
     *
     * @return ApiResponse
     */
    public function get(string $customerId, $bankAccountId = null, array $additionalParams = []): ApiResponse
    {
        $path = '/v1/customers/' . $customerId . '/bank_accounts';

        if ($bankAccountId) {
            $path .= '/' . $bankAccountId;
        }

        if (!empty($additionalParams)) {
            $additionalParams = Http::buildHttpQuery($additionalParams);
            $path .= '?' . $additionalParams;
        }

        return $this->apiWrapper->get($path);
    }

    /**
     * Create customer bank account.
     *
     * @param string $customerId Customer Id.
     * @param array $data Payload.
     *
     * @return ApiResponse
     */
    public function generate(string $customerId, array $data): ApiResponse
    {
        $path = '/v1/customers/' . $customerId . '/bank_accounts';

        $headers = Header::generate('POST', $path, $data);

        return $this->apiWrapper->post($path, $data, true, $headers);
    }
}
