<?php

namespace Fintecture\Util;

use Fintecture\Fintecture;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\Crypto;

class Header
{
    /**
     * Generate headers for the request with the signature and the digest.
     *
     * @param string $method The name of the method using for the request.
     * @param string $path Path of targeted endpoint.
     * @param mixed $body The body of the request. It's used to generate the digest for the signature.
     * @param int $authMethod Auth method: 0 => App Id, 1 => Token, 2 => Basic Auth, 3 => Secure
     * @param array $customCredentials Custom credentials.
     *
     * @return array Generated headers for an HTTP query.
     *
     * @throws \Exception if we can't generate signature.
     */
    public static function generate(
        string $method,
        string $path,
        $body = null,
        int $authMethod = 1,
        array $customCredentials = null
    ): array {
        $appId = isset($customCredentials['appId']) ?
            $customCredentials['appId'] : Fintecture::getConfig()->getAppId();
        $appSecret = isset($customCredentials['appSecret']) ?
            $customCredentials['appSecret'] : Fintecture::getConfig()->getAppSecret();
        $privateKey = isset($customCredentials['privateKey']) ?
            $customCredentials['privateKey'] : Fintecture::getConfig()->getFinalPrivateKey();

        $date = date('r');
        $requestId = Crypto::uuid4();
        $headers = ['Accept' => 'application/json']; // init headers

        // Generate signature
        $signingString = '(request-target): ' . strtolower($method) . ' ' . $path . "\n";
        $signingString .= 'date: ' . $date . "\n";
        if ('POST' === $method && $authMethod !== 2) {
            $digest = 'SHA-256=' . Crypto::encodeToBase64($body, true);
            $signingString .= 'digest: ' . $digest . "\n";
            $headers['Digest'] = $digest;
            $headers['Content-Type'] = 'application/json';
        }
        $signingString .= 'x-request-id: ' . $requestId;
        if (!openssl_sign($signingString, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new \Exception('Signature can\'t be generated');
        }
        $signatureBase64 = base64_encode($signature);
        $signature = 'keyId="' . $appId . '",algorithm="rsa-sha256",headers="(request-target) date '
            . (isset($digest) ? 'digest ' : '') . 'x-request-id",signature="' . $signatureBase64 . '"';
        // Auth methods
        if ($authMethod === 1) {
            $token = Fintecture::getAccessToken();
            if ($token instanceof ApiResponse) {
                $headers['Authorization'] = 'Bearer ' . $token->access_token; /** @phpstan-ignore-line */
            } else {
                throw new \Exception('The access token is not set.');
            }
        } elseif ($authMethod === 2) {
            $headers['Authorization'] = 'Basic ' . base64_encode($appId . ':' . $appSecret);
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        } elseif ($authMethod === 3) {
            $headers['Authorization'] = 'Basic ' . base64_encode($appId . ':' . $appSecret);
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $headers['app_id'] = $appId;
        } else {
            $headers['app_id'] = $appId;
        }

        // Complete headers
        if ($authMethod !== 2) {
            $headers = array_merge($headers, [
                'User-Agent' => 'Fintecture PHP SDK v' . Fintecture::VERSION,
                'Signature' => $signature,
                'Date' => $date,
                'X-Request-Id' => $requestId
            ]);
        }

        return $headers;
    }
}
