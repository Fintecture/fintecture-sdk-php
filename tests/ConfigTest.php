<?php

namespace Fintecture\Tests;

use Fintecture\Config\Config;
use Fintecture\Util\FintectureException;

class ConfigTest extends Base
{
    public function testValidateGoodConfig(): void
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

    public function testValidateBadConfig(): void
    {
        $this->expectException(FintectureException::class);

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
