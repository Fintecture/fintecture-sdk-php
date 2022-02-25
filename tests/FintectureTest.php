<?php

namespace Fintecture\Tests;

use Fintecture\Fintecture;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class FintectureTest extends BaseTest
{
    public function testGetConfig(): void
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(is_null(Fintecture::getConfig()));
        Fintecture::setCurrentClient($currentClient);
    }

    public function testGetAccessToken(): void
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(is_null(Fintecture::getAccessToken()));
        Fintecture::setCurrentClient($currentClient);
    }

    public function testGetHttpClient(): void
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(Fintecture::getHttpClient() instanceof HttpClient);
        Fintecture::setCurrentClient($currentClient);
    }

    public function testGetRequestFactory(): void
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(Fintecture::getRequestFactory() instanceof RequestFactoryInterface);
        Fintecture::setCurrentClient($currentClient);
    }

    public function testGetStreamFactory(): void
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(Fintecture::getStreamFactory() instanceof StreamFactoryInterface);
        Fintecture::setCurrentClient($currentClient);
    }
}
