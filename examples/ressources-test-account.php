<?php

require_once('vendor/autoload.php');

// Step 1: create the client
// Note: ressources methods can also be used with AisClient
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

// Step 2: set the required data (optional)
$filters = [
    'filter[provider_id]' => 'xxxxxx'
];

// Step 3: get informations about your application
$pisTestAccount = $pisClient->testAccount->get($filters);
if (!$pisTestAccount->error) {
    var_dump($pisTestAccount);
} else {
    echo $pisTestAccount->errorMsg;
}
