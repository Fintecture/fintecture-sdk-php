<?php

namespace Fintecture\Tests;

class ApiWrapperTest extends BaseTest
{
    public function testGet(): void
    {
        $response = $this->apiWrapper->get('test', ['test' => 'test']);
        $this->assertFalse($response->error);
    }

    public function testPost(): void
    {
        $response = $this->apiWrapper->post('test', ['test' => 'test'], true, ['test' => 'test']);
        $this->assertFalse($response->error);
    }
}
