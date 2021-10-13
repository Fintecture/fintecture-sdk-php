<?php

namespace Fintecture\Tests;

use Fintecture\Util\Crypto;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class CryptoTest extends PHPUnitTestCase
{
    public function testEncodeToJson()
    {
        $this->assertTrue(gettype(Crypto::encodeToJson(['option1', 'option2'])) === 'string');
    }

    public function testEncodeToBase64()
    {
        $this->assertTrue(gettype(Crypto::encodeToBase64(['option1', 'option2'])) === 'string');
    }

    public function testHashAndEncodeToBase64()
    {
        $this->assertTrue(gettype(Crypto::encodeToBase64(['option1', 'option2'], true)) === 'string');
    }
}
