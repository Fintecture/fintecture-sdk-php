<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;

class PisTest extends BaseTest
{
    public function testPisConnect()
    {
        $connect = $this->pisClient->connect->generate(['data'], 'state', 'redirectUri', 'originUri');
        $this->assertTrue($connect instanceof ApiResponse);
    }

    public function testPisInitiate()
    {
        $initiate = $this->pisClient->initiate->generate(['data'], 'state', 'redirectUri', 'originUri');
        $this->assertTrue($initiate instanceof ApiResponse);
    }

    public function testPisPayment()
    {
        $payment = $this->pisClient->payment->get('sessionId');
        $this->assertTrue($payment instanceof ApiResponse);
    }

    public function testRefund()
    {
        $refund = $this->pisClient->refund->generate(['data']);
        $this->assertTrue($refund instanceof ApiResponse);
    }

    public function testRequestToPay()
    {
        $requestToPay = $this->pisClient->requestToPay->generate(['data'], 'redirectUri');
        $this->assertTrue($requestToPay instanceof ApiResponse);
    }

    public function testSettlement()
    {
        $settlement = $this->pisClient->settlement->get('settlementId', ['param' => true]);
        $this->assertTrue($settlement instanceof ApiResponse);
    }
}
