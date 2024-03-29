<?php

namespace Fintecture\Tests;

class ApiWrapperTest extends Base
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

    public function testPatch(): void
    {
        $response = $this->apiWrapper->patch('test', ['test' => 'test'], true, ['test' => 'test']);
        $this->assertFalse($response->error);
    }
}
