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
        'psu_phone' => '0601020304',
        'psu_phone_prefix' => '0033'
    ],
    'data' => [
        'type' => 'PIS',
        'attributes' => [
            'amount' => 550.60,
            'currency' => 'EUR',
            'communication' => 'Commande N°15654'
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

In the context of integrating Fintecture directly, you need to follow these steps: 

1- Get all providers
[Example to use the function getProviders()](https://github.com/Fintecture/fintecture-sdk-php/tree/master/example/example-get-providers.php)

2- Initiate payment
[Example to use the function postInitiate()](https://github.com/Fintecture/fintecture-sdk-php/tree/master/example/example-post-initiate.php)

Optional :
You can verify the status of the payment to see if the payment is done.

You have 2 method, if you have given an url to receive webhook, you need to wait a webhook but with the SDK you can validate the webhook. ([Example to use the function validateWebhook](https://github.com/Fintecture/fintecture-sdk-php/tree/master/example/example-validate-webhook.php)

The other method is to get the details of a payment.([Example to use the function getPayment()](https://github.com/Fintecture/fintecture-sdk-php/tree/master/example/example-get-payment.php)
