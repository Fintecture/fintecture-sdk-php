<?php

namespace Fintecture\Api\Resources;

use Fintecture\Fintecture;
use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Application extends Api
{
    /**
     * Get application details.
     *
     * @return ApiResponse Application details.
     */
    public function get(): ApiResponse
    {
        return $this->apiWrapper->get('res/v1/applications', null, 0);
    }
}
