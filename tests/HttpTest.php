<?php

namespace Fintecture\Tests;

use Fintecture\Util\Http;

class HttpTest extends Base
{
    public function testBuildHttpQuery(): void
    {
        $httpQueryString = Http::buildHttpQuery([
            'state' => 'random-id',
            'redirect_uri' => 'https://localhost/'
        ]);

        $httpQueryString2 = Http::buildHttpQuery([
            'state' => 'random-id',
            'with_beneficiary' => false,
            'with_virtual_beneficiary' => true,
        ]);

        $this->assertTrue($httpQueryString === 'state=random-id&redirect_uri=https%3A%2F%2Flocalhost%2F');
        $this->assertTrue($httpQueryString2 === 'state=random-id&with_beneficiary=false&with_virtual_beneficiary=true');
    }
}
