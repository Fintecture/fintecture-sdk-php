<?php

namespace Fintecture\Tests;

use Fintecture\Fintecture;
use Fintecture\Tests\BaseTest;

class ApiWrapperTest extends BaseTest
{
    public function testGet()
    {
        $response = $this->apiWrapper->get('test', ['test' => 'test']);
        $this->assertTrue($response->error);
    }

    public function testPost()
    {
        $response = $this->apiWrapper->post('test', ['test' => 'test'], 1, ['test' => 'test']);
        $this->assertTrue($response->error);
    }
}
