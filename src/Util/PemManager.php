<?php

namespace Fintecture\Util;

use Fintecture\Util\EncryptionManager;

class PemManager
{
    private $encryptionManager;

    public function __construct(EncryptionManager $encryptionManager = null)
    {
        $this->encryptionManager = $encryptionManager ?: null;
    }

    /**
     * Get the private key
     *
     * @param string $privateKey filename or content
     *
     * @return string private key
     */
    public function readPrivateKey(string $privateKey): string
    {
        if ($this->isPemFile($privateKey)) {
            $privateKey = file_get_contents($privateKey);
        }
        return trim($privateKey);
    }

    /**
     * Check format of private key. Decrypt it if needed, encrypt it if needed.
     *
     * @param string $privateKey
     *
     * @return array [
     *     'privateKey' => 'privateKey',
     *     'encrypted' => true
     * ]
     */
    public function formatPrivateKey(string $privateKey): array
    {
        $privateKey = preg_replace("/\n\r/m", "\n", $privateKey);
        if (!$this->encryptionManager) {
            return array('privateKey' => $privateKey, 'encrypted' => false);
        }

        // Decrypt private key if needed
        $decryptedPrivateKey = $this->encryptionManager->decryptContent($privateKey);
        if (!$decryptedPrivateKey) {
            // Encrypt pem file for better protection (useful if stored in database)
            return array(
                'privateKey' => $this->encryptionManager->encryptContent($privateKey),
                'encrypted' => true
            );
        }
        return array('privateKey' => $decryptedPrivateKey, 'encrypted' => false);
    }

    /**
     * Check content of PEM string
     *
     * @param string $content
     *
     * @return bool true if all went well, false if not
     */
    public function isPemString(string $content): bool
    {
        // Check if the content begins with the query below
        $query = '-----BEGIN PRIVATE KEY-----';
        if (substr($content, 0, strlen($query)) === $query) {
            return true;
        }
        return false;
    }

    /**
     * Check content of PEM string
     *
     * @param string $path path of the file
     *
     * @return bool true if all went well, false if not
     */
    private function isPemFile(string $path): bool
    {
        if (is_file($path)) {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            // Check extension + pem filesize is ~2Ko: don't accept files > 10Ko
            if ($extension === 'pem' && filesize($path) < 10000) {
                return true;
            }
        }
        return false;
    }
}
