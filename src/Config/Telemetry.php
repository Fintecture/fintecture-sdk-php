<?php

namespace Fintecture\Config;

use Fintecture\Fintecture;

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
        // Don't send a call if telemetry is disabled
        if (Fintecture::getConfig() && !Fintecture::getConfig()->getEnabledTelemetry()) {
            return false;
        }

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $body = self::getMetrics($action, $additionalMetrics);

        $apiWrapper = Fintecture::getApiWrapper() ?: Fintecture::setApiWrapper();
        $apiResponse = $apiWrapper->post(Fintecture::PRODUCTION_API_URL . 'ext/v1/activity', $body, true, $headers);
        return !$apiResponse->error; // true if no error, false if there is an error
    }

    /**
     * Logs a metric
     *
     * @param string $category
     *
     * @return bool Sending status.
     */
    public static function logMetric(string $category): bool
    {
        // Don't send a call if telemetry is disabled
        if (!Fintecture::getConfig() || !Fintecture::getConfig()->getEnabledTelemetry()) {
            return false;
        }

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $body = [
            'application_id' => Fintecture::getConfig()->getAppId(),
            'category' => $category
        ];

        $apiWrapper = Fintecture::getApiWrapper() ?: Fintecture::setApiWrapper();
        $apiResponse = $apiWrapper->post(Fintecture::PRODUCTION_API_URL . 'ext/v1/metric', $body, true, $headers);
        return !$apiResponse->error; // true if no error, false if there is an error
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
        $metrics = [
            'php_version' => phpversion(),
            'sdk_version' => Fintecture::VERSION,
            'action' => $action
        ];
        if ($additionalMetrics) {
            $metrics = array_merge($metrics, $additionalMetrics);
        }

        $metrics['type'] = isset($metrics['type']) ? $metrics['type'] : 'php-sdk-1';
        if (!isset($metrics['shop_name'])) {
            if (Fintecture::getConfig()) {
                $metrics['shop_name'] = Fintecture::getConfig()->getShopName();
            }
        }
        return $metrics;
    }
}
