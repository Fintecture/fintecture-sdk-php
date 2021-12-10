<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;

class ResourceTest extends BaseTest
{
    public function testResourceApplication(): void
    {
        $application = $this->aisClient->application->get();
        $this->assertTrue($application instanceof ApiResponse);
    }

    public function testAResourceProvider(): void
    {
        $provider = $this->aisClient->provider->get('id', ['param' => true]);
        $this->assertTrue($provider instanceof ApiResponse);
    }

    public function testAResourceTestAccount(): void
    {
        $testAccount = $this->aisClient->testAccount->get(['param' => true]);
        $this->assertTrue($testAccount instanceof ApiResponse);
    }
}
