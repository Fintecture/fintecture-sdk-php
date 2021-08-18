<?php

namespace Fintecture\Util;

use Fintecture\Util\FileSystem;

class EncryptionManager
{
    public const ENCRYPTION_METHOD = 'AES-128-CBC';
    public const ENCRYPTION_FILE_PREFIX = 'fintecture_key_';

    private $ivLen;
    private $directory;
    private $encryptionKey;

    public function __construct(string $directory = null)
    {
        $this->ivLen = openssl_cipher_iv_length(self::ENCRYPTION_METHOD);
        // Always have a slash at end of path if not null
        $this->directory = $directory ? rtrim($directory, '/') . '/' : null;
    }

    /**
     * Read encryption key
     *
     * @param string $encryptionKey Encryption Key
     *
     * @return bool status of operation.
     */
    public function initEncryptionKey(?string $encryptionKey = null)
    {
        if (!empty($encryptionKey)) {
            $this->encryptionKey = $encryptionKey;
            return true;
        }

        try {
            return $this->analyzeEncryptionKeyFiles();
        } catch (\Exception $e) {
            \trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return false;
    }

    /**
     * Analyse encryption dir to find the good encryption file
     *
     * @return bool status of operation.
     *
     * @throws \Exception if invalid directory or if we can't create encryption key file.
     */
    private function analyzeEncryptionKeyFiles()
    {
        if (!is_dir($this->directory)) {
            throw new \Exception('Invalid directory. Please verify the path.');
        }

        // Scan the directory to find files that starts by the file prefix
        $files = FileSystem::streamSafeGlob($this->directory, self::ENCRYPTION_FILE_PREFIX . '*');
        $nbFiles = count($files);
        if ($nbFiles === 1) {
            // If there is only one file, this is the file to use to get encryption key
            if ($fileContent = file_get_contents($files[0])) {
                $this->encryptionKey = hex2bin($fileContent);
                return true;
            }
        } elseif ($nbFiles > 1) {
            // If there are more than 1 file, it's not normal
            // The data could have been manipulated and we can't know which file to use
            // So delete them: the user will have to re-upload his keys
            foreach ($files as $file) {
                unlink($file);
            }
        }

        // No encryption key file, let generate one
        if ($this->createEncryptionKeyFile()) {
            return $this->analyzeEncryptionKeyFiles();
        } else {
            throw new \Exception('Can\'t generate encryption key file. Please verify file permissions.');
        }
    }

    /**
     * Create the encryption key file
     *
     * @return bool status of operation.
     */
    private function createEncryptionKeyFile(): bool
    {
        // Add a random key to the filename to avoid accessing it easily by guessing URL
        $filename = $this->directory . self::ENCRYPTION_FILE_PREFIX . bin2hex(openssl_random_pseudo_bytes(8)) . '.txt';
        $encryptionKey = openssl_random_pseudo_bytes(16);
        return file_put_contents($filename, bin2hex($encryptionKey));
    }

    /**
    * Works with PHP >= 5.4
    * There is another method with GCM instead of CBC (more secure) for PHP >= 7.1 described on the link below
    * https://www.php.net/manual/fr/function.openssl-encrypt.php
    */
    public function encryptContent(string $content): string
    {
        $iv = openssl_random_pseudo_bytes($this->ivLen); // cipher initialization vector
        $ciphertextRaw = openssl_encrypt($content, self::ENCRYPTION_METHOD, $this->encryptionKey, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertextRaw, $this->encryptionKey, $as_binary=true);
        $ciphertext = base64_encode($iv. $hmac . $ciphertextRaw);
        return $ciphertext;
    }

    public function decryptContent(string $content)
    {
        $decodedContent = base64_decode($content);
        $iv = substr($decodedContent, 0, $this->ivLen); // cipher initialization vector
        $hmac = substr($decodedContent, $this->ivLen, $sha2len=32);
        $ciphertextRaw = substr($decodedContent, $this->ivLen + $sha2len);
        $originalPlaintext = openssl_decrypt($ciphertextRaw, self::ENCRYPTION_METHOD, $this->encryptionKey, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertextRaw, $this->encryptionKey, $as_binary=true);

        if ($originalPlaintext) {
            return hash_equals($hmac, $calcmac) ? $originalPlaintext : false; // timing attack safe comparison
        } else {
            // Content is not encrypted
            return false;
        }
    }
}
