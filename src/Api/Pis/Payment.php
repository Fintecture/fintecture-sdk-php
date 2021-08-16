<?php

namespace Fintecture\Api\Pis;

use Fintecture\Fintecture;
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
}
