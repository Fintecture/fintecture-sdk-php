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
        'psu_phone' => '601020304',
        'psu_phone_prefix' => '+33',
        'psu_address' => [
            'street_number' => '5',
            'street' => 'Parvis',
            'zipcode' => '78180',
            'city' => 'Montigny le Bretonneux',
            'country' => 'FR'
        ],
        'cc' => 'john@doe.com',
        'bcc' => 'john@doe.com',
        'expirary' => 86400
    ],
    'data' => [
        'type' => 'REQUEST_TO_PAY',
        'attributes' => [
            'amount' => 289.89,
            'currency' => 'EUR',
            'communication' => 'FINTECTURE-6495933'
        ],
   ],
];

$redirectUri = 'https://domain.com';

// Step 3: Generate a requestToPay call
$pisRequestToPay = $pisClient->requestToPay->generate($payload, $redirectUri);
if (!$pisRequestToPay->error) {
    var_dump($pisRequestToPay);
} else {
    echo $pisRequestToPay->errorMsg;
}
