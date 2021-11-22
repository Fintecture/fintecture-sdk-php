<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;

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
