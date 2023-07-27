<?php

namespace Fintecture\Api;

use Fintecture\Fintecture;

abstract class Api
{
    /**
     * @var ApiWrapper
     */
    protected $apiWrapper;

    public function __construct()
    {
        $this->apiWrapper = Fintecture::getApiWrapper() ?: Fintecture::setApiWrapper();
    }
}
