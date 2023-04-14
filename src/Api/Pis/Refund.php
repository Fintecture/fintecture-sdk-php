<?php

namespace Fintecture\Api\Pis;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Http;

class Refund extends Api
{
    /**
     * Initiate a refund.
     *
     * @param array $data Payload.
     * @param string $state State.
     *
     * @return ApiResponse Generated refund.
     */
    public function generate(array $data, string $state = null): ApiResponse
    {
        $params = Http::buildHttpQuery([
            'state' => $state
        ]);
        $path = '/pis/v2/refund';
        if ($params) {
            $path .= '?' . $params;
        }


        return $this->apiWrapper->post($path, $data);
    }
}
