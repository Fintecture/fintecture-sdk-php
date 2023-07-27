<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;
use Fintecture\Fintecture;
use Fintecture\PisClient;
use Fintecture\Util\FintectureException;
use Http\Mock\Client as MockClient;
use Psr\Http\Message\ResponseInterface;

class ClientTest extends Base
{
    public function testNotWellFormattedPrivateKey(): void
    {
        $this->expectException(FintectureException::class);
        $pisClient = new PisClient([
            'appId' => 'test',
            'appSecret' => 'test',
            'privateKey' => 'notWellFormattedKey',
            'environment' => 'sandbox'
        ], new MockClient());
    }

    public function testEncryptedPrivateKey(): void
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

    public function testInvalidGet(): void
    {
        $this->expectException(FintectureException::class);
        $this->aisClient->fake; /** @phpstan-ignore-line */
    }

    public function testInvalidRedirect(): void
    {
        $this->expectException(FintectureException::class);
        $this->aisClient->redirect('badurl');
    }

    public function testGetEncryptedPrivateKey(): void
    {
        $this->assertTrue(gettype($this->pisClient->getEncryptedPrivateKey()) === 'string');
    }

    public function testGetFinalPrivateKey(): void
    {
        $this->assertTrue(gettype($this->pisClient->getFinalPrivateKey()) === 'string');
    }

    public function testSetAccessToken(): void
    {
        $token = Fintecture::getAccessToken();

        /** @var ResponseInterface $response */
        $response = $this->createMock('Psr\Http\Message\ResponseInterface');
        $newToken = new ApiResponse($response, (object) ['access_token' => 'token']);
        $this->aisClient->setAccessToken($newToken);
        $this->assertTrue(Fintecture::getAccessToken() === $newToken);
    }
}
