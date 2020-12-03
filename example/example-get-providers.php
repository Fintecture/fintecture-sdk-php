<?php

require_once __DIR__ . '/vendor/autoload.php';

$myPrivateKey = 'app_privateKey-app2.pem'; // Private Key path downloaded from the Fintecture Console (https://console.fintecture.com/)
$app_id = '<my_app_id>'; // App ID available in the Fintecture Console (https://console.fintecture.com/)
$app_secret = '<my_app_secret>'; // App Secret available in the Fintecture Console (https://console.fintecture.com/)

$myClient = new Fintecture\Client([
    'FINTECTURE_OAUTH_URL' => 'https://oauth-sandbox.fintecture.com',
    'FINTECTURE_PIS_URL' => 'https://api-sandbox.fintecture.com',
    'FINTECTURE_CONNECT_URL' => 'https://connect-sandbox.fintecture.com',
    'FINTECTURE_PRIVATE_KEY' => preg_replace("/\n\r/m", '\n', file_get_contents($myPrivateKey)),
    'FINTECTURE_APP_ID' => $app_id,
    'FINTECTURE_APP_SECRET' => $app_secret,
]);

$paramsProviders = [
    'filter[country]' => 'FR' ,
    'filter[pis]' => 'SEPA',
    'filter[ais]' => 'Accounts',    
    'filter[psu_type]' => 'retail',
    'filter[auth_model]' => 'redirect',
    'sort[name]' => 'ASC',
    'sort[full_name]' => 'ASC',
    'sort[country]' => 'ASC',
    'sort[provider_id]' => 'ASC'
];

try {    
    $myResponse = $myClient->getProviders($paramsProviders);

    var_dump($myResponse);    
}
catch (Exception $e) {
    var_dump($e->getMessage());
}
