<?php

namespace Fintecture\Api\Resources;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class TestAccount extends Api
{
    /**
     * Get tests accounts of banks by environment.
     *
     * @param array $additionalParams Additional parameters.
     *     $params = [
     *         'filter[provider_id]' => (string)
     *     ]
     *
     * @return ApiResponse Tests accounts.
     */
    public function get(array $additionalParams = []): ApiResponse
    {
        $path = 'res/v1/testaccounts';
        if (!empty($additionalParams)) {
            $additionalParams = http_build_query($additionalParams);
            $path .= '?' . $additionalParams;
        }

        return $this->apiWrapper->get($path, null, 0);
    }
}
