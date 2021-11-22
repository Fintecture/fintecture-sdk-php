<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;
use Fintecture\Fintecture;
use Fintecture\Util\Header;

class HeaderTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();

        $token = new ApiResponse([], (object) ['access_token' => 'token']);
        Fintecture::setAccessToken($token);
    }

    public function testGenerateWithClientToken()
    {
        $params = http_build_query([
            'state' => 'random-id',
            'redirect_uri' => 'https://localhost/'
        ]);
        $path = '/pis/v2/connect?' . $params;

        $headers = Header::generate('GET', $path);

        $this->assertTrue(gettype($headers) === 'array');
        $this->assertTrue(count($headers) === 6);
        $this->assertTrue(isset($headers['Authorization']));
    }

    public function testGenerateWithAppID()
    {
        $params = http_build_query([
            'state' => 'random-id',
            'redirect_uri' => 'https://localhost/'
        ]);
        $path = '/pis/v2/connect?' . $params;

        $headers = Header::generate('GET', $path, null, 0);

        $this->assertTrue(gettype($headers) === 'array');
        $this->assertTrue(count($headers) === 6);
        $this->assertTrue(isset($headers['app_id']));
    }

    public function testGenerateWithClientTokenAndBody()
    {
        $params = http_build_query([
            'state' => 'random-id',
            'redirect_uri' => 'https://localhost/'
        ]);
        $path = '/pis/v2/connect?' . $params;

        $headers = Header::generate('POST', $path, $this->payload);

        $this->assertTrue(gettype($headers) === 'array');
        $this->assertTrue(count($headers) === 8);
        $this->assertTrue(isset($headers['Authorization']));
    }

    public function testGenerateWithAppIDAndBody()
    {
        $params = http_build_query([
            'state' => 'random-id',
            'redirect_uri' => 'https://localhost/'
        ]);
        $path = '/pis/v2/connect?' . $params;

        $headers = Header::generate('POST', $path, $this->payload, 0);

        $this->assertTrue(gettype($headers) === 'array');
        $this->assertTrue(count($headers) === 8);
        $this->assertTrue(isset($headers['app_id']));
    }

    public function testGenerateWithAppIDAndTokenAndBody()
    {
        $path = '/oauth/secure/accesstoken';

        $headers = Header::generate('POST', $path, $this->payload, 3);

        $this->assertTrue(gettype($headers) === 'array');
        $this->assertTrue(count($headers) === 9);
        $this->assertTrue(isset($headers['app_id']));
        $this->assertTrue(isset($headers['Authorization']));
    }
}
