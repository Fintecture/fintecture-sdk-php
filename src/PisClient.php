<?php

namespace Fintecture;

use Fintecture\Api\Pis\Assessment;
use Fintecture\Api\Pis\Connect as PisConnect;
use Fintecture\Api\Pis\Initiate;
use Fintecture\Api\Pis\Payment;
use Fintecture\Api\Pis\Refund;
use Fintecture\Api\Pis\RequestForPayout;
use Fintecture\Api\Pis\RequestToPay;
use Fintecture\Api\Pis\Settlement;

/**
 * @property Assessment $assessment
 * @property Initiate $initiate
 * @property Payment $payment
 * @property PisConnect $connect
 * @property Refund $refund
 * @property RequestForPayout $requestForPayout
 * @property RequestToPay $requestToPay
 * @property Settlement $settlement
 */
class PisClient extends Client
{
    /**
     * @var string Identifier of the client.
     */
    protected $identifier = 'pis';
}
