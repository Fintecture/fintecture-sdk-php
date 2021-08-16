<?php

namespace Fintecture\Config;

use Fintecture\Fintecture;

class Endpoint
{
    /**
     * Get current environment.
     *
     * @return string Current environment.
     */
    private static function getCurrentEnvironment(): string
    {
        return Fintecture::getConfig()->getEnvironment();
    }

    /**
     * Get main API url.
     *
     * @param string $environment Environment to check.
     *
     * @return string Main API URL.
     */
    public static function getApiUrl(string $environment = null): string
    {
        $environment = $environment ?: self::getCurrentEnvironment();
        switch ($environment) {
            case 'sandbox':
                return Fintecture::SANDBOX_API_URL;
            case 'production':
                return Fintecture::PRODUCTION_API_URL;
            default:
                return Fintecture::SANDBOX_API_URL;
        }
    }
}
