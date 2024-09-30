<?php

namespace Fintecture\Tests;

use Fintecture\Api\ApiResponse;

class CustomersTest extends Base
{
    public function testCustomersCustomers(): void
    {
        $customers = $this->pisClient->customers->get();
        $this->assertTrue($customers instanceof ApiResponse);

        $customers = $this->pisClient->customers->get('customerId', ['param' => true]);
        $this->assertTrue($customers instanceof ApiResponse);

        $customers = $this->pisClient->customers->generate(['data']);
        $this->assertTrue($customers instanceof ApiResponse);
    }

    public function testCustomersCustomerBankAccount(): void
    {
        $customerBankAccount = $this->pisClient->customerBankAccount->get('customerId');
        $this->assertTrue($customerBankAccount instanceof ApiResponse);

        $customerBankAccount = $this->pisClient->customerBankAccount->get('customerId', ['param' => true]);
        $this->assertTrue($customerBankAccount instanceof ApiResponse);

        $customerBankAccount = $this->pisClient->customerBankAccount->get('customerId', 'bankAccountId', ['param' => true]);
        $this->assertTrue($customerBankAccount instanceof ApiResponse);

        $customerBankAccount = $this->pisClient->customerBankAccount->generate('customerId', ['data']);
        $this->assertTrue($customerBankAccount instanceof ApiResponse);
    }
}
