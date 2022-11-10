<?php

namespace Fintecture\Tests;

use Fintecture\Config\Endpoint;
use Fintecture\Fintecture;

class EndpointTest extends BaseTest
{
    public function testUrls(): void
    {
        // "Sandbox" test
        $this->assertTrue(Endpoint::getApiUrl() == Fintecture::SANDBOX_API_URL);

        // "Test" test
        Fintecture::getConfig()->setEnvironment('test');
        $this->assertTrue(Endpoint::getApiUrl() == Fintecture::TEST_API_URL);

        // "Production" test
        Fintecture::getConfig()->setEnvironment('production');
        $this->assertTrue(Endpoint::getApiUrl() == Fintecture::PRODUCTION_API_URL);

        // "No value" test
        Fintecture::getConfig()->setEnvironment('');
        $this->assertTrue(Endpoint::getApiUrl() == Fintecture::SANDBOX_API_URL);

        // Reset env to "sandbox"
        Fintecture::getConfig()->setEnvironment('sandbox');
    }
}
