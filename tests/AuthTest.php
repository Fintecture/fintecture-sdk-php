<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;

class AuthTest extends BaseTest
{
    public function testAuthToken(): void
    {
        $aisToken = $this->aisClient->token->generate('ais');
        $this->assertTrue($aisToken instanceof ApiResponse);

        $pisToken = $this->pisClient->token->generate('pis');
        $this->assertTrue($pisToken instanceof ApiResponse);
    }

    public function testAuthRefreshToken(): void
    {
        $refreshToken = $this->aisClient->token->refresh('token');
        $this->assertTrue($refreshToken instanceof ApiResponse);
    }
}
