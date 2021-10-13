<?php

namespace Fintecture\Tests;

use Fintecture\Fintecture;
use Fintecture\Api\ApiResponse;
use Fintecture\Tests\BaseTest;

class ApiResponseTest extends BaseTest
{
    public function testGetProperty()
    {
        $apiResponse = new ApiResponse([], (object) ['key' => true]);
        $this->assertTrue($apiResponse->key); /** @phpstan-ignore-line */
    }

    public function testGetWrongProperty()
    {
        $apiResponse = new ApiResponse([], (object) ['key' => true]);
        $this->expectException(\Exception::class);
        $apiResponse->fake; /** @phpstan-ignore-line */
    }
}
