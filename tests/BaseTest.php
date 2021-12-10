<?php

namespace Fintecture\Tests;

use Fintecture\AisClient;
use Fintecture\Api\ApiResponse;
use Fintecture\Api\ApiWrapper;
use Fintecture\Fintecture;
use Fintecture\PisClient;
use Http\Mock\Client as MockClient;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ResponseInterface;

abstract class BaseTest extends PHPUnitTestCase
{
    /** @var \org\bovigo\vfs\vfsStreamDirectory $root */
    public $root;

    /** @var string $dataPath */
    public $dataPath;

    /** @var string $privateKey */
    public $privateKey;

    /** @var string $privateKeyPath */
    public $privateKeyPath;

    /** @var string $encryptedPrivateKey */
    public $encryptedPrivateKey;

    /** @var AisClient $aisClient */
    public $aisClient;

    /** @var PisClient $pisClient */
    public $pisClient;

    /** @var ApiWrapper $apiWrapper */
    public $apiWrapper;

    /** @var array $payload */
    public $payload = [
        'meta' => [
            // Info of the buyer
            'psu_name' => 'M. John Doe',
            'psu_email' => 'john@doe.com',
            'psu_address' => [
                'street' => '5 Void Street',
                'zip' => '12345',
                'city' => 'Gotham',
                'country' => 'FR'
            ]
        ],
        'data' => [
            'type' => 'SEPA',
            'attributes' => [
                'amount' => '550.60',
                'currency' => 'EUR',
                'communication' => 'Commande N°15654'
            ]
        ]
    ];

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('encryption-keys');

        $this->dataPath = __DIR__ . '/data/';

        $this->privateKeyPath = $this->dataPath . 'private_key.pem';
        $this->privateKey = file_get_contents($this->privateKeyPath);
        $this->encryptedPrivateKey = file_get_contents($this->dataPath . 'encrypted_private_key.txt');

        /** @var ResponseInterface $response */
        $response = $this->createMock('Psr\Http\Message\ResponseInterface');
        $newToken = new ApiResponse($response, (object) ['access_token' => 'token']);

        $this->aisClient = new AisClient([
            'appId' => 'test',
            'appSecret' => 'test',
            'privateKey' => $this->privateKeyPath,
            'environment' => 'test',
            'enabledTelemetry' => 0
        ], new MockClient());
        $this->aisClient->setAccessToken($newToken);

        $this->pisClient = new PisClient([
            'appId' => 'test',
            'appSecret' => 'test',
            'privateKey' => $this->privateKeyPath,
            'environment' => 'sandbox',
            'encryptionDir' => vfsStream::url('encryption-keys'),
            'enabledTelemetry' => 1
        ], new MockClient());
        $this->pisClient->setAccessToken($newToken);

        $this->apiWrapper = Fintecture::getApiWrapper();
    }
}
