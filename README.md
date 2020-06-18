Fintecture is a Fintech that has a payment solution via bank transfer available at [https://www.fintecture.com/].
This library is a PHP Client for the Fintecture API. With it you can initiate a payment via the solution PayByBank of Fintecture

Send an email to anjan@fintecture.com to get the full API documentation

Installation
============

Official installation method is via composer and its packagist package [fintecture/fintecture-sdk-php](https://packagist.org/packages/fintecture/fintecture-sdk-php).

```
$ composer require fintecture/fintecture-sdk-php
```

Usage
=====

The simplest usage of the library would be as follow:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$myPrivateKey = 'app_privateKey.pem'; // Private Key path downloaded from the Fintecture Console (https://console.fintecture.com/)
$app_id = '<my_app_id>'; // App ID available in the Fintecture Console (https://console.fintecture.com/)
$app_secret = '<my_app_secret>'; // App Secret available in the Fintecture Console (https://console.fintecture.com/)
$state = '<my-uniq-id-for-the-payment>'; // It's my ID, I have to generate it myself, it will be sent back in the callback

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
        'psu_address' => [
            'street' => '5 Void Street',
            'zip' => '12345',
            'city' => 'Gotham',
            'country' => 'FR',
        ],
    ],
    'data' => [
        'type' => 'SEPA',
        'attributes' => [
            'amount' => 550.60,
            'currency' => 'EUR',
            'communication' => 'Commande N°15654',
            'beneficiary' => [
                'name' => 'World Company',
                'street' => 'power street',
                'number' => '3',
                'city' => 'Atlantis',
                'zip' => '12345',
                'country' => 'FR',
                'iban' => '798787987988982789',
                'swift_bic' => 'BIC456',
                'bank_name' => 'WEALTHY BANK',
            ],
        ],
    ],
];

$myResponse = $myClient->generateConnectURL($data, $state);
if($myResponse['error']['code'] < 0) {
    // ERROR
} else {
    // Redirection to the connect url (The buyer will see his bank login page to perform the bak transfer
    header('Location: ' . $myResponse['data']['connect_url']);
}
```