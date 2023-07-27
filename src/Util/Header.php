<?php

namespace Fintecture\Util;

use Fintecture\Api\ApiResponse;
use Fintecture\Fintecture;

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
        if (isset($customCredentials['appId']) && isset($customCredentials['appSecret']) && isset($customCredentials['privateKey'])) {
            $appId = $customCredentials['appId'];
            $appSecret = $customCredentials['appSecret'];
            $privateKey = $customCredentials['privateKey'];
        } else {
            $config = Fintecture::getConfig();
            if (!$config) {
                throw new FintectureException('Header needs a configured client');
            }
            $appId = $config->getAppId();
            $appSecret = $config->getAppSecret();
            $privateKey = $config->getFinalPrivateKey();
        }

        $date = date('r');
        $requestId = Crypto::uuid4();
        $headers = ['Accept' => 'application/json']; // init headers

        // Build digest if needed
        if (('POST' === $method || 'PATCH' === $method) && $authMethod !== 2) {
            $digest = 'SHA-256=' . Crypto::encodeToBase64($body, true);
            $headers['Digest'] = $digest;
            $headers['Content-Type'] = 'application/json';
        }

        $signature = self::generateSignature([
            'appId' => $appId,
            'date' => $date,
            'digest' => isset($digest) ? $digest : '',
            'method' => $method,
            'path' => $path,
            'privateKey' => $privateKey,
            'requestId' => $requestId
        ]);

        // Auth methods
        if ($authMethod === 1 || $authMethod === 4) {
            $token = Fintecture::getAccessToken();
            if ($token instanceof ApiResponse) {
                $headers['Authorization'] = 'Bearer ' . $token->access_token; /** @phpstan-ignore-line */
            } else {
                throw new FintectureException('The access token is not set.');
            }

            if ($authMethod === 4) {
                $headers['app_id'] = $appId;
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

    /**
     * Generate signature
     *
     * @param array $data Required information to generate signature.
     *
     * @return string Generated signature.
     */
    public static function generateSignature(array $data): string
    {
        // Generate signature
        $signingString = '(request-target): ' . strtolower($data['method']) . ' ' . $data['path'] . "\n";
        $signingString .= 'date: ' . $data['date'] . "\n";
        if (!empty($data['digest'])) {
            $signingString .= 'digest: ' . $data['digest'] . "\n";
        }
        $signingString .= 'x-request-id: ' . $data['requestId'];
        if (!openssl_sign($signingString, $signature, $data['privateKey'], OPENSSL_ALGO_SHA256)) {
            throw new FintectureException('Signature can\'t be generated.');
        }
        return self::generateFullSignature($data, $signature);
    }

    /**
     * Generate full signature
     *
     * @param array $data Data.
     * @param string $signature Signature.
     *
     * @return string Full generated signature.
     */
    public static function generateFullSignature(array $data, string $signature): string
    {
        $signatureBase64 = base64_encode($signature);
        $signature = 'keyId="' . $data['appId'] . '",algorithm="rsa-sha256",headers="(request-target) date '
            . (!empty($data['digest']) ? 'digest ' : '') . 'x-request-id",signature="' . $signatureBase64 . '"';
        return $signature;
    }
}
