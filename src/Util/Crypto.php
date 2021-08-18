<?php

namespace Fintecture\Util;

class Crypto
{
    public const JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /**
     * Returns a json encoded string.
     *
     * @param array $body array to encode
     * @return string encoded string
     */
    public static function encodeToJson(array $body): string
    {
        return json_encode($body, self::JSON_OPTIONS);
    }

    /**
     * Returns a base64 encoded string (hash or not).
     *
     * @param mixed $body array to encode
     * @param bool $hash whether hash or not the data
     * @return string encoded string, hashed or not
     */
    public static function encodeToBase64($body, $hash=false): string
    {
        if (is_array($body)) {
            $body = self::encodeToJson($body);
        }
        if ($hash) {
            $body = hash('sha256', $body, true); // Set to true to get a binary format output (default hex)
        }
        return base64_encode($body);
    }

    /**
     * This function is used to create a UUIDv4.
     *
     * @return string UUID4
     */
    public static function uuid4(): string
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
