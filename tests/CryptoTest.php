<?php

namespace Fintecture\Tests;

use Fintecture\Util\Crypto;
use PHPUnit\Framework\TestCase;

class CryptoTest extends TestCase
{
    public function testEncodeToJson(): void
    {
        $this->assertTrue(gettype(Crypto::encodeToJson(['option1', 'option2'])) === 'string');
    }

    public function testEncodeToBase64(): void
    {
        $this->assertTrue(gettype(Crypto::encodeToBase64(['option1', 'option2'])) === 'string');
    }

    public function testHashAndEncodeToBase64(): void
    {
        $this->assertTrue(gettype(Crypto::encodeToBase64(['option1', 'option2'], true)) === 'string');
    }
}
