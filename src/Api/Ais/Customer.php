<?php

namespace Fintecture\Api\Ais;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Customer extends Api
{
    /**
     * Delete all data of a customer.
     *
     * @param string $customerId Customer Id.
     *
     * @return ApiResponse Confirmation.
     */
    public function delete(string $customerId): ApiResponse
    {
        $path = '/ais/v1/customer/' . $customerId;

        return $this->apiWrapper->delete($path);
    }
}
