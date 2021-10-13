<?php

namespace Fintecture\Tests;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Fintecture\Fintecture;
use Fintecture\Api\ApiResponse;

class FintectureTest extends BaseTest
{
    public function testGetConfig()
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(is_null(Fintecture::getConfig()));
        Fintecture::setCurrentClient($currentClient);
    }

    public function testGetAccessToken()
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(is_null(Fintecture::getAccessToken()));
        Fintecture::setCurrentClient($currentClient);
    }

    public function testGetMessageFactory()
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(Fintecture::getMessageFactory() instanceof MessageFactory);
        Fintecture::setCurrentClient($currentClient);
    }

    public function testGetHttpClient()
    {
        $currentClient = Fintecture::getCurrentClient();
        Fintecture::setCurrentClient('badclient');
        $this->assertTrue(Fintecture::getHttpClient() instanceof HttpClient);
        Fintecture::setCurrentClient($currentClient);
    }
}
