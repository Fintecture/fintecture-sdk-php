<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;

class AisTest extends BaseTest
{
    public function testAisAccount(): void
    {
        $account = $this->aisClient->account->get('customerId', 'accountId', ['param' => true]);
        $this->assertTrue($account instanceof ApiResponse);
    }

    public function testAisAccountHolder(): void
    {
        $accountHolder = $this->aisClient->accountHolder->get('customerId', ['param' => true]);
        $this->assertTrue($accountHolder instanceof ApiResponse);
    }

    public function testAisAuthorize(): void
    {
        $authorize = $this->aisClient->authorize->generate('providerId', 'redirectUri', true);
        $this->assertTrue($authorize instanceof ApiResponse);

        $authorizeWoToken = $this->aisClient->authorize->generate('providerId', 'redirectUri', false);
        $this->assertTrue($authorizeWoToken instanceof ApiResponse);
    }

    public function testAisAuthorizeDecoupled(): void
    {
        $authorize = $this->aisClient->authorize->generateDecoupled('providerId', 'pollingId', 'xPsuId', 'xPsuIpAddress', true);
        $this->assertTrue($authorize instanceof ApiResponse);

        $authorizeWoToken = $this->aisClient->authorize->generateDecoupled('providerId', 'pollingId', 'xPsuId', 'xPsuIpAddress', false);
        $this->assertTrue($authorizeWoToken instanceof ApiResponse);
    }

    public function testAisConnect(): void
    {
        $connect = $this->aisClient->connect->generate('redirectUri', 'state', 'pis');
        $this->assertTrue($connect instanceof ApiResponse);
    }

    public function testAisCustomer(): void
    {
        $customer = $this->aisClient->customer->delete('customerId');
        $this->assertTrue($customer instanceof ApiResponse);
    }

    public function testAisTransaction(): void
    {
        $transaction = $this->aisClient->transaction->get('customerId', 'accountId', ['param' => true]);
        $this->assertTrue($transaction instanceof ApiResponse);
    }
}
