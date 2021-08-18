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

// Step 2: get informations about your application
$aisApplication = $aisClient->application->get();
if (!$aisApplication->error) {
    var_dump($aisApplication);
} else {
    echo $aisApplication->errorMsg;
}
