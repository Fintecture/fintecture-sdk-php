<?php

namespace Fintecture\Tests;

use Fintecture\Config\Telemetry;
use Fintecture\Fintecture;

class TelemetryTest extends Base
{
    public function testLogAction(): void
    {
        $this->assertTrue(Telemetry::logAction('test', ['additionalConfig' => 'test']));

        // Disable telemetry
        if ($config = Fintecture::getConfig()) {
            $config->setEnabledTelemetry(false);
            $this->assertFalse(Telemetry::logAction('test'));
        }

        // Re-enable telemetry
        if ($config = Fintecture::getConfig()) {
            $config->setEnabledTelemetry(true);
        }
    }

    public function testLogMetric(): void
    {
        $this->assertTrue(Telemetry::logMetric('test'));

        // Disable telemetry
        if ($config = Fintecture::getConfig()) {
            $config->setEnabledTelemetry(false);
            $this->assertFalse(Telemetry::logMetric('test'));
        }

        // Re-enable telemetry
        if ($config = Fintecture::getConfig()) {
            $config->setEnabledTelemetry(true);
        }
    }
}
