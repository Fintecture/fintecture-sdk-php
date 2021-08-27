<?php

namespace Fintecture\Config;

use Fintecture\Fintecture;
use Fintecture\Config\Endpoint;

class Telemetry
{
    /**
     * Logs an action
     *
     * @param string $action Action to log.
     * @param array $additionalMetrics Additional metrics.
     *
     * @return bool Sending status.
     */
    public static function logAction(string $action, array $additionalMetrics = null): bool
    {
        if (
            !Fintecture::getCurrentClient()
            || !Fintecture::getHttpClient()
            || !Fintecture::getConfig()
            || !Fintecture::getConfig()->getEnabledTelemetry()
        ) {
            return false;
        }

        $headers = array(
            'Content-Type' => 'application/json'
        );

        $body = self::getMetrics($action, $additionalMetrics);

        $apiWrapper = Fintecture::getApiWrapper() ?: Fintecture::setApiWrapper();
        $apiWrapper->post('https://api.fintecture.com/ext/v1/activity/', $body, true, $headers);
        return true;
    }

    /**
     * Returns the configuration summary of the application.
     *
     * @param string $action Action to log.
     * @param array $additionalMetrics Additional metrics.
     *
     * @return array Configuration.
     */
    private static function getMetrics(string $action, array $additionalMetrics = null): array
    {
        // Construct configuration return
        $metrics = array(
            'identifier' => Fintecture::getConfig()->getAppName(),
            'php_version' => phpversion(),
            'action' => $action
        );
        if ($additionalMetrics) {
            $metrics = array_merge($metrics, $additionalMetrics);
        }
        return $metrics;
    }
}
