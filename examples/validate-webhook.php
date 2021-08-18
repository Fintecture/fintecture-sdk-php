<?php

require_once('vendor/autoload.php');

// The validation of a webhook is firstly based on an action performed by the user,
// for example a payment made with our connect.
// Once the user has validated his payment, your webhook url will be called after a short
// delay defined in your app and will send it informations allowing you to verify the payment.

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
$state = '<my-uniq-id-for-the-payment>'; // my unique identifier for the transaction
$payload = []; // check examples/pis-connect.php for example data

// Step 3: generate a connect instance with redirection on success
$pisConnect = $pisClient->connect->generate($payload, $state);
if (!$pisConnect->error) {
    $pisClient->redirect($pisConnect->meta->url);
} else {
    echo $pisConnect->errorMsg;
}

// Step 4: receive webhook event after x minutes (defined in your app at console.fintecture.com)

/**
 * Webhook
 * Webhook route have to be specified in your app at console.fintecture.com
 *
 * @Route("/webhook", methods={"POST"})
 */
public function webhook()
{
    // Get server vars sent to the webhook
    $body = file_get_contents('php://input');
    $digest = stripslashes($_SERVER['HTTP_DIGEST']);
    $signature = stripslashes($_SERVER['HTTP_SIGNATURE']);

    // Verify these vars with the SDK
    $validSignature = \Fintecture\Util\Validation::validSignature($body, $digest, $signature);

    if ($validSignature) {
        // Response can be handled safely
        var_dump($_POST); // you should have received some informations from our API
    } else {
        exit('Invalid signature');
    }
}
