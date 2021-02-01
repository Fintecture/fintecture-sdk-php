<?php

require_once __DIR__ . '/vendor/autoload.php';

$myPrivateKey = 'app_privateKey-app2.pem'; // Private Key path downloaded from the Fintecture Console (https://console.fintecture.com/)
$app_id = '<my_app_id>'; // App ID available in the Fintecture Console (https://console.fintecture.com/)
$app_secret = '<my_app_secret>'; // App Secret available in the Fintecture Console (https://console.fintecture.com/)
$state = str_shuffle('Irandomastringtogeneratemyid'); // It's my ID, I have to generate it myself, it will be sent back in the callback

$providerId = 'cmcifrpp';
$redirectUri = 'https://console.fintecture.com';

$myClient = new Fintecture\Client([
    'FINTECTURE_OAUTH_URL' => 'https://oauth-sandbox.fintecture.com',
    'FINTECTURE_PIS_URL' => 'https://api-sandbox.fintecture.com',
    'FINTECTURE_CONNECT_URL' => 'https://connect-sandbox.fintecture.com',
    'FINTECTURE_PRIVATE_KEY' => preg_replace("/\n\r/m", '\n', file_get_contents($myPrivateKey)),
    'FINTECTURE_APP_ID' => $app_id,
    'FINTECTURE_APP_SECRET' => $app_secret,
]);

$data = [
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

try {
    $myResponse = $myClient->postRequestToPay($data, $redirectUri);
    var_dump($myResponse);    
}
catch (Exception $e) {
    var_dump($e->getMessage());
}