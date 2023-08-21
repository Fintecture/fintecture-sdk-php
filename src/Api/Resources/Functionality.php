<?php

namespace Fintecture\Api\Resources;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Functionality extends Api
{
    /**
     * Get functionality status.
     */
    public function get(string $functionalityId): ApiResponse
    {
        return $this->apiWrapper->get('res/v1/functionalities/' . $functionalityId, null, 4);
    }
}
