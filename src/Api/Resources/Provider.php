<?php

namespace Fintecture\Api\Resources;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Http;

class Provider extends Api
{
    /**
     * Get Provider(s).
     *
     * @param string $id Id of the provider.
     * @param array $additionalParams Additional parameters.
     *     $additionalParams = [
     *         'filter[country]' => (string)
     *         'filter[ais]' => (string)
     *         'filter[pis]' => (string)
     *         'filter[psu_type]' => (string) retail / corporate
     *         'filter[auth_model]' => (string) redirect / decoupled
     *         'sort[name]' => (string) ASC / DESC
     *         'sort[full_name]' => (string) ASC / DESC
     *         'sort[country]' => (string) ASC / DESC
     *         'sort[provider_id]' => (string) ASC / DESC
     *     ]
     *
     * @return ApiResponse Provider(s) infos.
     */
    public function get(string $id = null, array $additionalParams = []): ApiResponse
    {
        $path = 'res/v1/providers' . ($id ? '/' . $id : '');
        if (!empty($additionalParams)) {
            $additionalParams = Http::buildHttpQuery($additionalParams);
            $path .= '?' . $additionalParams;
        }

        return $this->apiWrapper->get($path, null, 0);
    }
}
