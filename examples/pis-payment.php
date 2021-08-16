<?php

require_once('vendor/autoload.php');

// Step 1: create the client
$pisClient = new \Fintecture\PisClient([
    'appId' => '',
    'appSecret' => '',
    'privateKey' => '', // could be a file path or the private key itself
    'environment' => 'sandbox'
]);

$pisToken = $pisClient->token->generate();
if (!$pisToken->error) {
    $pisClient->setAccessToken($pisToken); // set token of PIS client
} else {
    echo $pisToken->errorMsg;
}

// Step 2: get payment infos
$sessionId = 'xxxxxx'; // session id
$pisPayment = $pisClient->payment->get($sessionId);

if (!$pisPayment->error) {
    var_dump($pisPayment);
} else {
    echo $pisPayment->errorMsg;
}
