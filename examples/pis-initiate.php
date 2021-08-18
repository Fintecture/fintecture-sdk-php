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
        'psu_address' => [
            'street' => 'Void Street',
            'number' => '3',
            'zip' => '12345',
            'city' => 'Gotham',
            'country' => 'FR',
        ],
        'psu_phone' => '0601020304',
        'psu_phone_prefix' => '0033'
    ],
    'data' => [
        'type' => 'PIS',
        'attributes' => [
            'amount' => '550.60',
            'currency' => 'EUR',
            'communication' => 'Commande N15654',
            'beneficiary' => [
                'name' => 'World Company',
                'street' => 'power street',
                'number' => '3',
                'city' => 'Atlantis',
                'zip' => '12345',
                'country' => 'FR',
                'iban' => 'FR7630001007941234567890185',
                'swift_bic' => 'BDFEFRPPCCT'
            ],
        ],
    ],
];

$providerId = 'xxxxxx';
$redirectUri = 'https://domain.com';
$state = uniqid(); // my unique identifier for the transaction

// Step 3: Generate an initiate call
$pisInitiate = $pisClient->initiate->generate($payload, $providerId, $redirectUri, $state);
if (!$pisInitiate->error) {
    var_dump($pisInitiate);
} else {
    echo $pisInitiate->errorMsg;
}
