# PHP library for the Fintecture API.

[![Latest Stable Version](http://poser.pugx.org/fintecture/fintecture-sdk-php/v)](https://packagist.org/packages/fintecture/fintecture-sdk-php) [![Total Downloads](http://poser.pugx.org/fintecture/fintecture-sdk-php/downloads)](https://packagist.org/packages/fintecture/fintecture-sdk-php) [![Monthly Downloads](http://poser.pugx.org/fintecture/fintecture-sdk-php/d/monthly)](https://packagist.org/packages/fintecture/fintecture-sdk-php) [![Latest Unstable Version](http://poser.pugx.org/fintecture/fintecture-sdk-php/v/unstable)](https://packagist.org/packages/fintecture/fintecture-sdk-php) [![License](http://poser.pugx.org/fintecture/fintecture-sdk-php/license)](https://packagist.org/packages/fintecture/fintecture-sdk-php)

Fintecture is a Fintech that has a payment solution via bank transfer available at [fintecture.com](https://www.fintecture.com/).

This library is a PHP Client for the Fintecture API.

## Requirements

* PHP >= 7.1

## Quick install

Via [Composer](https://getcomposer.org), with our packagist package [fintecture/fintecture-sdk-php](https://packagist.org/packages/fintecture/fintecture-sdk-php).

This command will get you up and running quickly with a Guzzle HTTP client.

```bash
composer require fintecture/fintecture-sdk-php guzzlehttp/guzzle http-interop/http-factory-guzzle
```

## Getting started

Simple usage looks like:

```php
require_once('vendor/autoload.php');

$state = '<my-uniq-id-for-the-payment>'; // it's my transaction ID, I have to generate it myself, it will be sent back in the callback
$pisClient = new \Fintecture\PisClient([
    'appId' => 'app_id',
    'appSecret' => 'app_secret',
    'privateKey' => 'private_key', // could be a file path or the private key itself
    'environment' => 'sandbox' // or 'production'
]);

$pisToken = $pisClient->token->generate();
if (!$pisToken->error) {
    $pisClient->setAccessToken($pisToken); // set token of PIS client
} else {
    echo $pisToken->errorMsg;
}

$payload = [
    'meta' => [
        // Info of the buyer
        'psu_name' => 'M. John Doe',
        'psu_email' => 'john@doe.com',
        'psu_address' => [
            'street' => '5 Void Street',
            'zip' => '12345',
            'city' => 'Gotham',
            'country' => 'FR'
        ]
    ],
    'data' => [
        'type' => 'SEPA',
        'attributes' => [
            'amount' => '550.60',
            'currency' => 'EUR',
            'communication' => 'Commande N°15654'
        ]
    ]
];

$connect = $pisClient->connect->generate($payload, $state);
if (!$connect->error) {
    $pisClient->redirect($connect->meta->url);
} else {
    echo $connect->errorMsg;
}
```

### Available options of Client

- 'appId' => 'app_id',
- 'appSecret' => 'app_secret',
- 'privateKey' => 'private_key', // could be a file path or the private key itself
- 'environment' => 'sandbox' // or 'production'
- 'shopName' => 'My super shop', // don't forget to give your client a nice name (even if it's optional)

### Examples

Some examples (including webhook handling) are available in the [*examples* folder](https://github.com/Fintecture/fintecture-sdk-php/tree/master/examples).

## Advanced usage

We are decoupled from any HTTP messaging client with help by [HTTPlug](https://httplug.io).
A list of community provided clients is found here: https://packagist.org/providers/php-http/client-implementation

### Using a different http client

```bash
composer require fintecture/fintecture-sdk-php symfony/http-client nyholm/psr7
```

To set up the Fintecture client with this HTTP client

```php
use Fintecture\PisClient;
use Symfony\Component\HttpClient\HttplugClient;

$pisClient = new PisClient([$config], new HttplugClient());
```

## Available methods

These methods follow our [API structure](https://docs.fintecture.com/v2).

### Auth
- token
    - generate
    - refresh

### AIS
- account
    - get
- accountHolder
    - get
- authorize
    - generate
    - generateDecoupled
- connect
    - generate
- customer
    - delete
- transaction
    - get

### PIS
- connect
    - generate
- initiate
    - generate
- payment
    - get
- refund
    - generate
- requestToPay
    - generate
- settlement
    - get

### Resources
- application
    - get
- provider
    - get
- testAccount
    - get

## Development

### Requirements

- PHP
- Git
- Composer
- Make
- Xdebug (for test coverage)

### Initialize project for development

```bash
make init
```

### PHPUnit (Unit Tests)

Then you can run the tests:

```bash
make test
```

To generate the test coverage report, you can run this command:

```bash
make test-coverage
```

### PHPStan (Static Analysis)

There are 9 levels (0-8). Level is set in `phpstan.neon`.
```bash
make analyse
```

### PHP CS Fixer (Coding Standards)

```bash
make format
```

## Troubleshooting

Encountering an issue? Please contact us at developer@fintecture.com.

## License

Fintecture PHP API Client is an open-sourced software licensed under the [MIT license](LICENSE).