<?php

namespace Fintecture\Api\Resources;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Http;

class Application extends Api
{
    /**
     * Get application details.
     * @param array $additionalParams Additional parameters.
     *      $additionalParams = [
     *         'with_payment_methods' => (bool)
     *      ]
     *
     * @return ApiResponse Application details.
     */
    public function get(array $additionalParams = []): ApiResponse
    {
        $path = '/res/v1/applications';
        if (!empty($additionalParams)) {
            $additionalParams = Http::buildHttpQuery($additionalParams);
            $path .= '?' . $additionalParams;
        }
        return $this->apiWrapper->get($path, null, 0);
    }
}
