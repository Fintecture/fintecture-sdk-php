<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;
use Fintecture\Util\FintectureException;
use Psr\Http\Message\ResponseInterface;

class ApiResponseTest extends Base
{
    public function testGetProperty(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->createMock('Psr\Http\Message\ResponseInterface');
        $apiResponse = new ApiResponse($response, (object) ['key' => true]);
        $this->assertTrue($apiResponse->key); /** @phpstan-ignore-line */
    }

    public function testGetWrongProperty(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->createMock('Psr\Http\Message\ResponseInterface');
        $apiResponse = new ApiResponse($response, (object) ['key' => true]);
        $this->expectException(FintectureException::class);
        $apiResponse->fake; /** @phpstan-ignore-line */
    }
}
