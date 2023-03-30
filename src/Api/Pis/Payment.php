<?php

namespace Fintecture\Api\Pis;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Payment extends Api
{
    /**
     * Get payments of all transfers or of a specific transfer.
     *
     * @param string $sessionId Session Id.
     *
     * @return ApiResponse Payments.
     */
    public function get(string $sessionId = null): ApiResponse
    {
        $path = '/pis/v2/payments';
        if ($sessionId) {
            $path .= '/' . $sessionId;
        }

        return $this->apiWrapper->get($path);
    }

    /**
     * Update a specific payment.
     *
     * @param string $sessionId Session Id.
     * @param array $data Payload.
     *
     * @return ApiResponse Payment.
     */
    public function update(string $sessionId, array $data): ApiResponse
    {
        $path = '/pis/v2/payments/' . $sessionId;

        return $this->apiWrapper->patch($path, $data);
    }
}
