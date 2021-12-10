<?php

namespace Fintecture\Tests;

use Fintecture\Fintecture;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;

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

    public function testGetMessageFactory(): void
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(Fintecture::getMessageFactory() instanceof MessageFactory);
        Fintecture::setCurrentClient($currentClient);
    }

    public function testGetHttpClient(): void
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(Fintecture::getHttpClient() instanceof HttpClient);
        Fintecture::setCurrentClient($currentClient);
    }
}
