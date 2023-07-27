<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;

class PisTest extends Base
{
    public function testPisAssessment(): void
    {
        $assessment = $this->pisClient->assessment->get('siren', 550.60);
        $this->assertTrue($assessment instanceof ApiResponse);
    }

    public function testPisConnect(): void
    {
        $connect = $this->pisClient->connect->generate(['data'], 'state', 'redirectUri', 'originUri');
        $this->assertTrue($connect instanceof ApiResponse);
    }

    public function testPisConnectWithAdditionalHeaders(): void
    {
        $connect = $this->pisClient->connect->generate(['data'], 'state', 'redirectUri', 'originUri', true, null, ['x-psu-type' => 'corporate']);
        $this->assertTrue($connect instanceof ApiResponse);
    }

    public function testPisInitiate(): void
    {
        $initiate = $this->pisClient->initiate->generate(['data'], 'state', 'redirectUri', 'originUri');
        $this->assertTrue($initiate instanceof ApiResponse);
    }

    public function testPisPayment(): void
    {
        $payment = $this->pisClient->payment->get();
        $this->assertTrue($payment instanceof ApiResponse);

        $payment = $this->pisClient->payment->get('sessionId');
        $this->assertTrue($payment instanceof ApiResponse);

        $payment = $this->pisClient->payment->get('sessionId', false, true);
        $this->assertTrue($payment instanceof ApiResponse);

        $payment = $this->pisClient->payment->update('sessionId', ['data']);
        $this->assertTrue($payment instanceof ApiResponse);
    }

    public function testPisRefund(): void
    {
        $refund = $this->pisClient->refund->generate(['data']);
        $this->assertTrue($refund instanceof ApiResponse);

        $refundWithState = $this->pisClient->refund->generate(['data'], 'state');
        $this->assertTrue($refundWithState instanceof ApiResponse);
    }

    public function testPisRequestForPayout(): void
    {
        $requestForPayout = $this->pisClient->requestForPayout->generate(['data'], 'https://test.fr', 'state', 'fr', 'fr');
        $this->assertTrue($requestForPayout instanceof ApiResponse);
    }

    public function testPisRequestToPay(): void
    {
        $requestToPay = $this->pisClient->requestToPay->generate(['data'], 'fr', 'https://test.fr', 'state');
        $this->assertTrue($requestToPay instanceof ApiResponse);
    }

    public function testPisSettlement(): void
    {
        $settlement = $this->pisClient->settlement->get('settlementId', ['param' => true]);
        $this->assertTrue($settlement instanceof ApiResponse);
    }
}
