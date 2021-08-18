<?php

namespace Fintecture\Api\Pis;

use Fintecture\Fintecture;
use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Refund extends Api
{
    /**
     * Initiate a refund.
     *
     * @param array $data Payload.
     *
     * @return ApiResponse Generated refund.
     */
    public function generate(array $data): ApiResponse
    {
        $path = '/pis/v2/refund';

        return $this->apiWrapper->post($path, $data);
    }
}
