<?php

namespace Fintecture\Tests;

use Fintecture\Config\Config;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class ConfigTest extends BaseTest
{
    public function testValidateGoodConfig()
    {
        $config = [
            'appId' => 'test',
            'appSecret' => 'test',
            'privateKey' => $this->dataPath . 'private_key.pem',
            'environment' => 'sandbox'
        ];
        $config = new Config($config);
        $this->assertTrue($config->validate());
    }

    public function testValidateBadConfig()
    {
        $this->expectException(\Exception::class);

        $config = [
            'appId' => '',
            'appSecret' => '',
            'privateKey' => '',
            'environment' => 'bad',
            'encryptionDir' => 'badfolder'
        ];
        $config = new Config($config);
        $config->validate();
    }
}
