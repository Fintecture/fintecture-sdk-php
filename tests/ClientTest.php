<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;
use Fintecture\Fintecture;
use Fintecture\PisClient;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\Error\Error;

class ClientTest extends BaseTest
{
    public function testNotWellFormattedPrivateKey()
    {
        $this->expectException(\Exception::class);
        $pisClient = new PisClient([
            'appId' => 'test',
            'appSecret' => 'test',
            'privateKey' => 'notWellFormattedKey',
            'environment' => 'sandbox'
        ], new MockClient());
    }

    public function testEncryptedPrivateKey()
    {
        $pisClient = new PisClient([
            'appId' => 'test',
            'appSecret' => 'test',
            'privateKey' => $this->encryptedPrivateKey,
            'environment' => 'sandbox',
            'encryptionDir' => $this->dataPath
        ], new MockClient());

        $this->assertTrue($pisClient instanceof PisClient);
    }

    public function testInvalidGet()
    {
        $this->expectException(Error::class);
        $this->aisClient->fake; /** @phpstan-ignore-line */
    }

    /**
     * @runInSeparateProcess
     */
    public function testRedirect()
    {
        $this->aisClient->redirect('https://fintecture.com');
        $this->assertContains(
            'Location: https://fintecture.com',
            xdebug_get_headers()
        );
    }

    public function testInvalidRedirect()
    {
        $this->expectException(\Exception::class);
        $this->aisClient->redirect('badurl');
    }

    public function testGetEncryptedPrivateKey()
    {
        $this->assertTrue(gettype($this->pisClient->getEncryptedPrivateKey()) === 'string');
    }

    public function testGetFinalPrivateKey()
    {
        $this->assertTrue(gettype($this->pisClient->getFinalPrivateKey()) === 'string');
    }

    public function testSetAccessToken()
    {
        $token = Fintecture::getAccessToken();

        $newToken = new ApiResponse([], (object) ['access_token' => 'token']);
        $this->aisClient->setAccessToken($newToken);
        $this->assertTrue(Fintecture::getAccessToken() === $newToken);
    }
}
