<?php

require_once('vendor/autoload.php');

// Step 1: create the client
$aisClient = new \Fintecture\AisClient([
    'appId' => '',
    'appSecret' => '',
    'privateKey' => '', // could be a file path or the private key itself
    'environment' => 'sandbox'
]);

// Step 2: get an authorization code
$provider = 'xxxxxx'; // provider id
$redirectUrl = 'https://domain.com/webkook'; // this url will receive 'code' and 'customer_id'

$aisAuthorize = $aisClient->authorize->generate($provider, $redirectUrl);
if (!$aisAuthorize->error) {
    $aisClient->redirect($connect->result->url);
} else {
    echo $aisAuthorize->errorMsg;
}

// Step 3: get an authorization code
$code = 'received-code'; // use the code sent to your webhook
$aisToken = $aisClient->token->generate('ais', $code);
if (!$aisToken->error) {
    $aisClient->setAccessToken($aisToken); // set token of AIS client
} else {
    echo $aisToken->errorMsg;
}

// Step 4: get accounts of the customer
$customerId = 'received-customerId'; // use the customerId sent to your webhook
$aisAccount = $aisClient->account->get($customerId);
if (!$aisAccount->error) {
    $accounts = $aisAccount->data;
} else {
    echo $aisAccount->errorMsg;
}
