<?php

namespace Fintecture\Tests;

use Fintecture\Config\Telemetry;
use Fintecture\Fintecture;

class TelemetryTest extends BaseTest
{
    public function testLogAction(): void
    {
        $this->assertFalse(Telemetry::logAction('test', ['additionalConfig' => 'test']));

        // Disable telemetry
        Fintecture::getConfig()->setEnabledTelemetry(false);
        $this->assertFalse(Telemetry::logAction('test'));

        // Re-enable telemetry
        Fintecture::getConfig()->setEnabledTelemetry(true);
    }

    public function testLogMetric(): void
    {
        $this->assertFalse(Telemetry::logMetric('test'));

        // Disable telemetry
        Fintecture::getConfig()->setEnabledTelemetry(false);
        $this->assertFalse(Telemetry::logMetric('test'));

        // Re-enable telemetry
        Fintecture::getConfig()->setEnabledTelemetry(true);
    }
}
