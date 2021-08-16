<?php

require_once('vendor/autoload.php');

// Step 1: create the client
// Note: ressources methods can also be used with PisClient
$aisClient = new \Fintecture\AisClient([
    'appId' => '',
    'appSecret' => '',
    'privateKey' => '', // could be a file path or the private key itself
    'environment' => 'sandbox'
]);

$aisToken = $aisClient->token->generate();
if (!$aisToken->error) {
    $aisClient->setAccessToken($aisToken); // set token of AIS client
} else {
    echo $aisToken->errorMsg;
}

// Step 2 (method 1) : get provider infos
$provider = 'xxxxxx'; // provider id

$aisProvider = $aisClient->provider->get($provider);

// Step 2 (method 2) : get providers with filters
$paramsProviders = [
    'filter[country]' => 'FR',
    'filter[pis]' => 'SEPA',
    'filter[ais]' => 'Accounts',
    'filter[psu_type]' => 'retail',
    'filter[auth_model]' => 'redirect',
    'sort[name]' => 'ASC',
    'sort[full_name]' => 'ASC',
    'sort[country]' => 'ASC',
    'sort[provider_id]' => 'ASC'
];

$aisProvider = $aisClient->provider->get(null, $paramsProviders);

// Step 3: enjoy results!
if (!$aisProvider->error) {
    var_dump($aisProvider);
} else {
    echo $aisProvider->errorMsg;
}
