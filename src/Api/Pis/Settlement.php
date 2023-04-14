<?php

namespace Fintecture\Api\Pis;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Http;

class Settlement extends Api
{
    /**
     * Get all disbursements which occurred from your Local Acquiring account.
     *
     * @param string $settlementId Settlement Id.
     * @param array $additionalParams Additional parameters.
     *     $params = [
     *         'filter[date_to]' => (string) format: yyyy-mm-dd
     *         'filter[date_from]' => (string) format: yyyy-mm-dd
     *     ]
     *
     * @return ApiResponse Disbursements.
     */
    public function get(string $settlementId = null, array $additionalParams = []): ApiResponse
    {
        $path = '/pis/v2/settlements';
        if ($settlementId) {
            $path .= '/' . $settlementId;
        }
        if (!empty($additionalParams)) {
            $additionalParams = Http::buildHttpQuery($additionalParams);
            $path .= '?' . $additionalParams;
        }
        return $this->apiWrapper->get($path);
    }
}
