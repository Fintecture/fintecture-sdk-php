<?php

namespace Fintecture\Tests;

use Fintecture\Config\Endpoint;
use Fintecture\Fintecture;

class EndpointTest extends Base
{
    public function testUrls(): void
    {
        // "Sandbox" test
        $this->assertTrue(Endpoint::getApiUrl() == Fintecture::SANDBOX_API_URL);

        // "Test" test
        if ($config = Fintecture::getConfig()) {
            $config->setEnvironment('test');
            $this->assertTrue(Endpoint::getApiUrl() == Fintecture::TEST_API_URL);
        }

        // "Production" test
        if ($config = Fintecture::getConfig()) {
            $config->setEnvironment('production');
            $this->assertTrue(Endpoint::getApiUrl() == Fintecture::PRODUCTION_API_URL);
        }

        // "No value" test
        if ($config = Fintecture::getConfig()) {
            $config->setEnvironment('');
            $this->assertTrue(Endpoint::getApiUrl() == Fintecture::SANDBOX_API_URL);
        }

        // Reset env to "sandbox"
        if ($config = Fintecture::getConfig()) {
            $config->setEnvironment('sandbox');
        }
    }
}
