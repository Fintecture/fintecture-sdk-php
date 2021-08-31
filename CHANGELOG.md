# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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