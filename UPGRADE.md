# Upgrade between SDK versions

##  2.0.0

This version is a rewrite of the SDK.

### PHP 7.1

To use this new version, your application need to be at least on PHP 7.1. Support for previous versions have been dropped.

### HTTP client

Previously, the SDK required Guzzle ^6. Now, you are free to use your preferred HTTP client thanks to HTTPlug.
For this, just check our README.md, there are examples for Guzzle and Symfony clients.

### New clients

The Fintecture client `Fintecture\Client` is now divided in two client:
* `Fintecture\AisClient` for **AIS operations**
* `Fintecture\PisClient` for **PIS operations**

It follows the structure of [our API](https://docs.fintecture.com/v2/).

The two clients (AisClient and PisClient) handle **Ressources** operations.

### Token

You now have to generate a token and associate to the client one time before your requests:

```php
$pisToken = $pisClient->token->generate();
if (!$pisToken->error) {
    $pisClient->setAccessToken($pisToken); // set token of PIS client
} else {
    echo $pisToken->errorMsg;
}
```

### Redirection

The clients have a new ```redirect``` function to simplify your integration:
```php
$aisClient->redirect('https://example.com');
```

### Final words

See the README for some explanations and see also the examples folder to see usage examples.