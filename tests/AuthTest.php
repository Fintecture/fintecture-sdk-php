<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;
use Fintecture\Tests\BaseTest;

class AuthTest extends BaseTest
{
    public function testAuthToken()
    {
        $aisToken = $this->aisClient->token->generate('ais');
        $this->assertTrue($aisToken instanceof ApiResponse);

        $pisToken = $this->pisClient->token->generate('pis');
        $this->assertTrue($pisToken instanceof ApiResponse);
    }

    public function testAuthRefreshToken()
    {
        $refreshToken = $this->aisClient->token->refresh('token');
        $this->assertTrue($refreshToken instanceof ApiResponse);
    }
}
