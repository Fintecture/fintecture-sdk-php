<?php

namespace Fintecture\Util;

class EncryptionManager
{
    public const ENCRYPTION_METHOD = 'AES-128-CBC';
    public const ENCRYPTION_LENGTH = 16;
    public const ENCRYPTION_FILE_PREFIX = 'fintecture_key_';

    /** @var int $ivLen */
    private $ivLen;

    /** @var string $directory */
    private $directory;

    /** @var string $encryptionKey */
    private $encryptionKey;

    public function __construct(string $directory)
    {
        $this->ivLen = openssl_cipher_iv_length(self::ENCRYPTION_METHOD) ?: self::ENCRYPTION_LENGTH;
        // Always have a slash at end of path
        $this->directory = rtrim($directory, '/') . '/';
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
            throw new FintectureException($e->getMessage());
        }
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
            throw new FintectureException('Invalid directory. Please verify the path.');
        }

        // Scan the directory to find files that starts by the file prefix
        $files = FileSystem::streamSafeGlob($this->directory, self::ENCRYPTION_FILE_PREFIX . '*');
        $nbFiles = count($files);
        if ($nbFiles === 1) {
            // If there is only one file, this is the file to use to get encryption key
            if ($fileContent = file_get_contents($files[0])) {
                $encryptionKey = hex2bin($fileContent);

                if ($encryptionKey) {
                    $this->encryptionKey = $encryptionKey;
                    return true;
                } else {
                    throw new FintectureException('Cannot get private key.');
                }
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
            throw new FintectureException("Can't generate encryption key file. Please verify file permissions.");
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
        $randomBytes = openssl_random_pseudo_bytes(8);
        if ($randomBytes) {
            $filename = $this->directory . self::ENCRYPTION_FILE_PREFIX . bin2hex($randomBytes) . '.txt';
            $encryptionKey = openssl_random_pseudo_bytes(16);
            if ($encryptionKey) {
                return (bool) file_put_contents($filename, bin2hex($encryptionKey));
            }
        }

        return false;
    }

    /**
    * Works with PHP >= 5.4
    * There is another method with GCM instead of CBC (more secure) for PHP >= 7.1 described on the link below
    * https://www.php.net/manual/fr/function.openssl-encrypt.php
    *
    * @return string|false
    */
    public function encryptContent(string $content)
    {
        $iv = openssl_random_pseudo_bytes($this->ivLen); // cipher initialization vector
        if ($iv) {
            $ciphertextRaw = openssl_encrypt($content, self::ENCRYPTION_METHOD, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
            if ($ciphertextRaw) {
                $hmac = hash_hmac('sha256', $ciphertextRaw, $this->encryptionKey, true);
                $ciphertext = base64_encode($iv. $hmac . $ciphertextRaw);
                return $ciphertext;
            }
        }

        return false;
    }

    /**
     * @return string|false
     */
    public function decryptContent(string $content)
    {
        $decodedContent = base64_decode($content);
        $iv = substr($decodedContent, 0, $this->ivLen); // cipher initialization vector
        $hmac = substr($decodedContent, $this->ivLen, $sha2len = 32);
        $ciphertextRaw = substr($decodedContent, $this->ivLen + $sha2len);
        $originalPlaintext = openssl_decrypt($ciphertextRaw, self::ENCRYPTION_METHOD, $this->encryptionKey, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertextRaw, $this->encryptionKey, $as_binary = true);

        if ($originalPlaintext) {
            return hash_equals($hmac, $calcmac) ? $originalPlaintext : false; // timing attack safe comparison
        } else {
            // Content is not encrypted
            return false;
        }
    }
}
