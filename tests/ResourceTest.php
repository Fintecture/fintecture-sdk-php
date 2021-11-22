<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;

class ResourceTest extends BaseTest
{
    public function testResourceApplication()
    {
        $application = $this->aisClient->application->get();
        $this->assertTrue($application instanceof ApiResponse);
    }

    public function testAResourceProvider()
    {
        $provider = $this->aisClient->provider->get('id', ['param' => true]);
        $this->assertTrue($provider instanceof ApiResponse);
    }

    public function testAResourceTestAccount()
    {
        $testAccount = $this->aisClient->testAccount->get(['param' => true]);
        $this->assertTrue($testAccount instanceof ApiResponse);
    }
}
