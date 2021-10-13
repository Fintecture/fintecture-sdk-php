<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;
use Fintecture\Tests\BaseTest;

class AisTest extends BaseTest
{
    public function testAisAccount()
    {
        $account = $this->aisClient->account->get('customerId', 'accountId', ['param' => true]);
        $this->assertTrue($account instanceof ApiResponse);
    }

    public function testAisAccountHolder()
    {
        $accountHolder = $this->aisClient->accountHolder->get('customerId', ['param' => true]);
        $this->assertTrue($accountHolder instanceof ApiResponse);
    }

    public function testAisAuthorize()
    {
        $authorize = $this->aisClient->authorize->generate('providerId', 'redirectUri', true);
        $this->assertTrue($authorize instanceof ApiResponse);

        $authorizeWoToken = $this->aisClient->authorize->generate('providerId', 'redirectUri', false);
        $this->assertTrue($authorizeWoToken instanceof ApiResponse);
    }

    public function testAisAuthorizeDecoupled()
    {
        $authorize = $this->aisClient->authorize->generateDecoupled('providerId', 'pollingId', 'xPsuId', 'xPsuIpAddress', true);
        $this->assertTrue($authorize instanceof ApiResponse);

        $authorizeWoToken = $this->aisClient->authorize->generateDecoupled('providerId', 'pollingId', 'xPsuId', 'xPsuIpAddress', false);
        $this->assertTrue($authorizeWoToken instanceof ApiResponse);
    }

    public function testAisConnect()
    {
        $connect = $this->aisClient->connect->generate('redirectUri', 'state', 'pis');
        $this->assertTrue($connect instanceof ApiResponse);
    }

    public function testAisCustomer()
    {
        $customer = $this->aisClient->customer->delete('customerId');
        $this->assertTrue($customer instanceof ApiResponse);
    }

    public function testAisTransaction()
    {
        $transaction = $this->aisClient->transaction->get('customerId', 'accountId', ['param' => true]);
        $this->assertTrue($transaction instanceof ApiResponse);
    }
}
