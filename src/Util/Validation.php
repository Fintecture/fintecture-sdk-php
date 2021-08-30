<?php

namespace Fintecture\Util;

use Fintecture\Fintecture;
use Fintecture\Util\Crypto;
use Fintecture\Config\Endpoint;

class Validation
{
    /**
     * Valid credentials.
     *
     * @param string $scope Scope.
     * @param array $credentials Credentials.
     *     $credentials = [
     *         'appId' => (string)
     *         'appSecret' => (string)
     *         'privateKey' => (string)
     *     ]
     * @param string $environment Environment.
     * @param string $code AIS code.
     *
     * @return bool Credentials status.
     */
    public static function validCredentials(
        string $scope,
        array $credentials,
        string $environment,
        string $code = ''
    ): bool {
        $scope = $scope === 'ais' ? 'AIS' : 'PIS';
        $grantType = $scope === 'AIS' ? 'authorization_code' : 'client_credentials';

        $body = array(
            'grant_type' => $grantType
        );
        if ($scope === 'AIS') {
            $body['code'] = $code;
        } else {
            $body['app_id'] = $credentials['appId'];
        }
        $body['scope'] = $scope;

        $path = '/oauth/secure/accesstoken';
        $headers = Header::generate('POST', $path, $body, 3, $credentials);

        $apiWrapper = Fintecture::getApiWrapper() ?: Fintecture::setApiWrapper();
        $url = Endpoint::getApiUrl($environment) . ltrim($path, '/');
        $token = $apiWrapper->post($url, $body, false, $headers, 3);

        return isset($token->result->access_token);
    }

    /**
     * Valid a signature.
     *
     * @param mixed $body The body to verify
     * @param string $digest The digest to verify
     * @param string $signature The signature to verify
     *
     * @return bool Signature status.
     */
    public static function validSignature($body, string $digest, string $signature): bool
    {
        $privateKey = Fintecture::getConfig()->getFinalPrivateKey();
        $privateKey = openssl_pkey_get_private($privateKey);

        $digestBody = 'SHA-256=' . Crypto::encodeToBase64($body, true);
        $digestHeader = stripslashes($digest);

        $extractedSignature = self::extractSignature($signature);
        if (!openssl_private_decrypt($extractedSignature, $decrypted, $privateKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            echo openssl_error_string() . "\n";
            return false;
        }

        $signingString = preg_split("/\n|\r\n?/", $decrypted);
        $digestSignature = str_replace('"', '', substr($signingString[1], 8)); // 0: date, 1: digest

        // match the digest calculated from the received payload, the digest found in the headers and the digest encoded from the signature
        $match = $digestBody == $digestSignature && $digestBody == $digestHeader;
        return $match;
    }

    /**
     * Extract 'signature' var from signature.
     *
     * @param string $signature Signature.
     *
     * @return string Part after signature=.
     */
    public static function extractSignature($signature): string
    {
        $signature = stripslashes($signature);
        $signature = str_replace('"', '', $signature);
        $signature = explode(',', $signature)[3]; // 0: keyId, 1: algorithm, 2: headers, 3: signature
        // Just keep the part after "signature="
        $signature = explode('signature=', $signature)[1];
        $signature = base64_decode($signature);
        return $signature;
    }
}
