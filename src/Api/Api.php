<?php

namespace Fintecture\Api;

use Fintecture\Fintecture;
use Fintecture\Api\ApiWrapper;
use Fintecture\Api\ApiResponse;

abstract class Api
{
    /**
     * @var ApiWrapper
     */
    protected $apiWrapper;

    public function __construct()
    {
        $this->apiWrapper = Fintecture::getApiWrapper();
    }
}
