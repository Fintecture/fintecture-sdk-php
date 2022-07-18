<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;

class PisTest extends BaseTest
{
    public function testPisConnect(): void
    {
        $connect = $this->pisClient->connect->generate(['data'], 'state', 'redirectUri', 'originUri');
        $this->assertTrue($connect instanceof ApiResponse);
    }

    public function testPisInitiate(): void
    {
        $initiate = $this->pisClient->initiate->generate(['data'], 'state', 'redirectUri', 'originUri');
        $this->assertTrue($initiate instanceof ApiResponse);
    }

    public function testPisPayment(): void
    {
        $payment = $this->pisClient->payment->get('sessionId');
        $this->assertTrue($payment instanceof ApiResponse);
    }

    public function testRefund(): void
    {
        $refund = $this->pisClient->refund->generate(['data']);
        $this->assertTrue($refund instanceof ApiResponse);

        $refundWithState = $this->pisClient->refund->generate(['data'], 'state');
        $this->assertTrue($refundWithState instanceof ApiResponse);
    }

    public function testRequestToPay(): void
    {
        $requestToPay = $this->pisClient->requestToPay->generate(['data'], 'redirectUri');
        $this->assertTrue($requestToPay instanceof ApiResponse);
    }

    public function testSettlement(): void
    {
        $settlement = $this->pisClient->settlement->get('settlementId', ['param' => true]);
        $this->assertTrue($settlement instanceof ApiResponse);
    }
}
