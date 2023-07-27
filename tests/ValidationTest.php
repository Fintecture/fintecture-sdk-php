<?php

namespace Fintecture\Tests;

use Fintecture\Config\Config;
use Fintecture\Fintecture;
use Fintecture\Util\Crypto;
use Fintecture\Util\FintectureException;
use Fintecture\Util\Header;
use Fintecture\Util\Validation;

class ValidationTest extends Base
{
    public function testValidCredentials(): void
    {
        /** @var Config */
        $config = Fintecture::getConfig();

        $valid = Validation::validCredentials(
            'pis',
            [
                'appId' => $config->getAppId(),
                'appSecret' => $config->getAppSecret(),
                'privateKey' => $config->getFinalPrivateKey()
            ],
            'sandbox'
        );
        $this->assertFalse($valid);

        $valid = Validation::validCredentials(
            'ais',
            [
                'appId' => $config->getAppId(),
                'appSecret' => $config->getAppSecret(),
                'privateKey' => $config->getFinalPrivateKey()
            ],
            'sandbox',
            'code'
        );
        $this->assertFalse($valid);
    }

    public function testInvalidSignature(): void
    {
        $digest = file_get_contents($this->dataPath . 'bad_digest.txt');
        $signature = file_get_contents($this->dataPath . 'bad_signature.txt');
        if ($digest && $signature) {
            $this->assertFalse(Validation::validSignature(['key' => 'value'], $digest, $signature));
        }
    }

    public function testValidSignature(): void
    {
        // This test simulate behavior of our webhook call

        /** @var Config */
        $config = Fintecture::getConfig();

        $privateKey = $config->getFinalPrivateKey();
        $privateKey = openssl_pkey_get_private($privateKey);
        if (!$privateKey) {
            throw new FintectureException('Cannot get private key.');
        }
        $publicKeyInfos = openssl_pkey_get_details($privateKey);
        if (!$publicKeyInfos) {
            throw new FintectureException('Cannot get public key infos.');
        }

        // Generate signature
        $signingString = 'date: ' . date('r') . "\n";
        $signingString .= 'digest: SHA-256=' . Crypto::encodeToBase64($this->payload, true) . "\n";
        if (!openssl_public_encrypt($signingString, $signature, $publicKeyInfos['key'], OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new FintectureException('Signature can\'t be generated.');
        }
        $signature = Header::generateFullSignature(['appId' => $config->getAppId()], $signature);

        $path = '/pis/v2/connect';
        $headers = Header::generate('POST', $path, $this->payload, 1, null);
        $valid = Validation::validSignature($this->payload, $headers['Digest'], $signature);
        $this->assertTrue($valid);
    }
}
