<?php

namespace Fintecture\Api;

use Fintecture\Api\Ais\Account;
use Fintecture\Api\Ais\AccountHolder;
use Fintecture\Api\Ais\Authorize;
use Fintecture\Api\Ais\Connect as AisConnect;
use Fintecture\Api\Ais\Customer;
use Fintecture\Api\Ais\Transaction;
use Fintecture\Api\Auth\Token;
use Fintecture\Api\Customers\CustomerBankAccount;
use Fintecture\Api\Customers\Customers;
use Fintecture\Api\Pis\Assessment;
use Fintecture\Api\Pis\Connect as PisConnect;
use Fintecture\Api\Pis\Initiate;
use Fintecture\Api\Pis\Payment;
use Fintecture\Api\Pis\Refund;
use Fintecture\Api\Pis\RequestForPayout;
use Fintecture\Api\Pis\RequestToPay;
use Fintecture\Api\Pis\Settlement;
use Fintecture\Api\Resources\Application;
use Fintecture\Api\Resources\Functionality;
use Fintecture\Api\Resources\Provider;
use Fintecture\Api\Resources\TestAccount;
use Fintecture\Util\FintectureException;

class ApiFactory
{
    /**
     * @var string Identifier of the client.
     */
    private $clientIdentifier;

    /**
     * @var array
     */
    private $apis = [];

    /**
     * @var array<string, string>
     */
    private $baseClassMap = [
        'application' => Application::class,
        'customerBankAccount' => CustomerBankAccount::class,
        'customers' => Customers::class,
        'functionality' => Functionality::class,
        'provider' => Provider::class,
        'testAccount' => TestAccount::class,
        'token' => Token::class
    ];

    /**
     * @var array<string, string>
     */
    private $aisClassMap = [
        'account' => Account::class,
        'accountHolder' => AccountHolder::class,
        'authorize' => Authorize::class,
        'connect' => AisConnect::class,
        'customer' => Customer::class,
        'transaction' => Transaction::class
    ];

    /**
     * @var array<string, string>
     */
    private $pisClassMap = [
        'assessment' => Assessment::class,
        'connect' => PisConnect::class,
        'initiate' => Initiate::class,
        'payment' => Payment::class,
        'refund' => Refund::class,
        'requestForPayout' => RequestForPayout::class,
        'requestToPay' => RequestToPay::class,
        'settlement' => Settlement::class
    ];

    public function __construct(string $clientIdentifier)
    {
        $this->clientIdentifier = $clientIdentifier;

        // Merge base classes in AIS & PIS clients for convenience reasons.
        $this->aisClassMap = array_merge($this->aisClassMap, $this->baseClassMap);
        $this->pisClassMap = array_merge($this->pisClassMap, $this->baseClassMap);
    }

    /**
     * Override __get function to call the requested API class.
     *
     * @param string $name Name of the API class.
     *
     * @throws \Exception if method is not defined
     */
    public function __get(string $name): Api
    {
        $apiClass = $this->getApiClass($name);
        if (null !== $apiClass) {
            if (!\array_key_exists($apiClass, $this->apis)) {
                $this->apis[$name] = new $apiClass();
            }
            return $this->apis[$name];
        }
        throw new FintectureException('Undefined API class: ' . $name);
    }

    /**
     * Get Api class from the $classMap array.
     *
     * @param string $name Name of the API class.
     */
    private function getApiClass(string $name): ?string
    {
        if (strpos($this->clientIdentifier, 'ais-') !== false) {
            $classMap = $this->aisClassMap;
        } elseif (strpos($this->clientIdentifier, 'pis-') !== false) {
            $classMap = $this->pisClassMap;
        } else {
            $classMap = $this->baseClassMap;
        }
        return \array_key_exists($name, $classMap) ? $classMap[$name] : null;
    }
}
