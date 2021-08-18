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

// Step 2: set the required data
$payload = [
    'meta' => [
        // Info of the buyer
        'psu_name' => 'M. John Doe',
        'psu_email' => 'john@doe.com',
        'psu_phone' => '0601020304',
        'psu_ip' => 'XX.XX.XX.XXX',
        'psu_address' => [
            'street' => '5 Void Street',
            'complement' => 'RDC',
            'zip' => '12345',
            'city' => 'Gotham',
            'country' => 'FR',
        ]
    ],
    'data' => [
        'type' => 'SEPA',
        'attributes' => [
            'amount' => '550.60',
            'currency' => 'EUR',
            'communication' => 'Order 15654'
        ],
    ],
];
$state = uniqid(); // my unique identifier for the transaction

// Step 3: generate a connect instance with redirection on success
$pisConnect = $pisClient->connect->generate($payload, $state);
if (!$pisConnect->error) {
    $pisClient->redirect($pisConnect->meta->url);
} else {
    echo $pisConnect->errorMsg;
}
