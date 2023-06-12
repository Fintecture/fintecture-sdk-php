<?php

namespace Fintecture;

use Fintecture\Api\Ais\Account;
use Fintecture\Api\Ais\AccountHolder;
use Fintecture\Api\Ais\Authorize;
use Fintecture\Api\Ais\Connect as AisConnect;
use Fintecture\Api\Ais\Customer;
use Fintecture\Api\Ais\Transaction;

/**
 * @property Account $account
 * @property AccountHolder $accountHolder
 * @property AisConnect $connect
 * @property Authorize $authorize
 * @property Customer $customer
 * @property Transaction $transaction
 */
class AisClient extends Client
{
    /**
     * @var string Identifier of the client.
     */
    protected $identifier = 'ais';
}
