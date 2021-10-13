<?php

namespace Fintecture\Tests;

use Fintecture\Fintecture;
use Fintecture\AisClient;
use Fintecture\PisClient;
use Fintecture\Api\ApiResponse;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Http\Mock\Client as MockClient;
use org\bovigo\vfs\vfsStream;

abstract class BaseTest extends PHPUnitTestCase
{
    public $root;

    public $dataPath;

    public $privateKey;
    public $privateKeyPath;
    public $encryptedPrivateKey;

    public $aisClient;
    public $pisClient;
    public $apiWrapper;

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

    protected function setUp()
    {
        $this->root = vfsStream::setup('encryption-keys');

        $this->dataPath = __DIR__ . '/data/';

        $this->privateKeyPath = $this->dataPath . 'private_key.pem';
        $this->privateKey = file_get_contents($this->privateKeyPath);
        $this->encryptedPrivateKey = file_get_contents($this->dataPath . 'encrypted_private_key.txt');

        $newToken = new ApiResponse([], (object) ['access_token' => 'token']);

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
