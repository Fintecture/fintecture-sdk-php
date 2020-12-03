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

$headersWebhook = [];
$body = file_get_contents('php://input');

if(!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if(substr($key, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
$headers = apache_request_headers();

try {
    $date    = new DateTime();
    $myResponse = $myClient->validateWebhook($headers,$body);
    
    $myfile = fopen("response_wehbook.txt", "w");
    fwrite($myfile, $date->format('Y-m-d H:i:s') . PHP_EOL . $myResponse);
    fclose($myfile);
}
catch (Exception $e) {
    var_dump($e->getMessage());
}
