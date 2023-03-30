# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.3.3]
- Add update method for Payment endpoint.

## [2.3.2]
- Add 'php-http/discovery' in 'allow-plugins' config.
- Better reporting for HTTP discovery.

## [2.3.1]
- Add "xLanguage" as an optional header of requestForPayout endpoint.

## [2.3.0]
- Add requestForPayout endpoint.

## [2.2.4]
- Support additionalHeaders in connect method.

## [2.2.3]
- Add "with_virtualbeneficiary" query param in connect call.

## [2.2.2]
- Add optional state param to refund call.

## [2.2.1]
- Some HTTP success codes were not handled.

## [2.2.0]
- Bump minimum PHP version to 7.2.

## [2.1.0]
- BC: Drop included Curl client.
- Adopt PSR-18 HTTP client.

## [2.0.11]
- Tested up to PHP 8.1.

## [2.0.10]
- Improve validation of HTTP requests.

## [2.0.9]
- Add an exception for null config when validating a signature.

## [2.0.8]
- Rename an internal field.
- Remove unuseful imports.

## [2.0.7]
- Fix RTP call.

## [2.0.6]
- Improve PHP 7.1 compatibility.
- Better handling of HTTP errors.
- Add new telemetry method.
- Add tests and dev instructions.

## [2.0.5]
- Fix sending of a call.

## [2.0.4]
- Always have a default HTTP client even if no client is set.
- Always have a default Message Factory even if no client is set.
- Set API wrapper for Validation and Telemetry classes even if no client is set.

## [2.0.3]
- Fix use of some methods with multiple clients.

## [2.0.2]
- Change url of an endpoint.

## [2.0.1]
- Disable telemetry at instance for now.

## [2.0.0]
See UPGRADE.md to update.
- PHP 7.1+.
- Rewrite of the SDK.
- Decoupled from any HTTP client.
