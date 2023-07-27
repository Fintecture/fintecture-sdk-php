<?php

namespace Fintecture\Config;

use Fintecture\Fintecture;
use Fintecture\Util\FintectureException;

class Config
{
    /**
     * @var string
     */
    private $shopName;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $finalPrivateKey;

    /**
     * @var string
     */
    private $encryptedPrivateKey;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $encryptionDir;

    /**
     * @var bool
     */
    private $enabledTelemetry;

    public function __construct(array $config)
    {
        $this->shopName = isset($config['shopName']) ? $config['shopName'] : '';
        $this->appId = isset($config['appId']) ? $config['appId'] : '';
        $this->appSecret = isset($config['appSecret']) ? $config['appSecret'] : '';
        $this->privateKey = isset($config['privateKey']) ? $config['privateKey'] : '';
        $this->environment = isset($config['environment']) ? $config['environment'] : Fintecture::DEFAULT_ENV;
        $this->encryptionDir = isset($config['encryptionDir']) ? $config['encryptionDir'] : '';
        $this->enabledTelemetry = isset($config['enabledTelemetry']) ? boolval($config['enabledTelemetry']) : true;
    }

    /**
     * Get App Name.
     *
     * @return string Shop Name.
     */
    public function getShopName(): string
    {
        return $this->shopName;
    }

    /**
     * Get App Id.
     *
     * @return string App ID.
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * Get App Secret.
     *
     * @return string App Secret.
     */
    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    /**
     * Get Private Key provided by the user.
     * Could be plain, encrypted or even a filename.
     *
     * @return string Original Private Key.
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * Get the final used plain Private Key.
     *
     * @return string Plain Private Key.
     */
    public function getFinalPrivateKey(): string
    {
        return $this->finalPrivateKey;
    }

    /**
     * Set the final Private Key.
     *
     * @param string $finalPrivateKey The plain Private Key.
     */
    public function setFinalPrivateKey(string $finalPrivateKey): void
    {
        $this->finalPrivateKey = $finalPrivateKey;
    }

    /**
     * Get the encrypted Private Key.
     *
     * @return string Encrypted Private Key.
     */
    public function getEncryptedPrivateKey(): ?string
    {
        return $this->encryptedPrivateKey;
    }

    /**
     * Set the encrypted Private Key.
     *
     * @param string $encryptedPrivateKey The encrypted Private Key.
     */
    public function setEncryptedPrivateKey(string $encryptedPrivateKey): void
    {
        $this->encryptedPrivateKey = $encryptedPrivateKey;
    }

    /**
     * Get the current environment.
     *
     * @return string The current environment.
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Set the current environment.
     *
     * @param string $environment The new current environment.
     */
    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * Get the encryption directory.
     *
     * @return string The current encryption directory.
     */
    public function getEncryptionDir(): string
    {
        return $this->encryptionDir;
    }

    /**
     * Get Enabled Telemetry.
     *
     * @return bool Telemetry status.
     */
    public function getEnabledTelemetry(): bool
    {
        return $this->enabledTelemetry;
    }

    /**
     * Set Enabled Telemetry.
     *
     * @param bool $enabledTelemetry Telemetry status.
     */
    public function setEnabledTelemetry(bool $enabledTelemetry): void
    {
        $this->enabledTelemetry = $enabledTelemetry;
    }

    /**
     * Check the validity of the provider configuration
     *
     * @return bool True if all went well.
     *
     * @throws \Exception if there is an error.
     */
    public function validate(): bool
    {
        $errors = [];
        // Required options
        if (!$this->validateAppId()) {
            $errors[] = 'Invalid App ID.';
        }
        if (!$this->validateAppSecret()) {
            $errors[] = 'Invalid App Secret.';
        }
        if (!$this->validatePrivateKey()) {
            $errors[] = 'Invalid Private Key.';
        }
        if (!$this->validateEnvironment()) {
            $errors[] = 'Invalid Environment.';
        }
        if (!$this->validateEncryptionDir()) {
            $errors[] = 'Invalid Encryption directory.';
        }

        if (!empty($errors)) {
            throw new FintectureException('Invalid configuration, Please verify it: ' . implode(' ', $errors));
        }
        return true;
    }

    private function validateAppId(): bool
    {
        if (empty($this->getAppId())) {
            return false;
        }
        return true;
    }

    private function validateAppSecret(): bool
    {
        if (empty($this->getAppSecret())) {
            return false;
        }
        return true;
    }

    private function validatePrivateKey(): bool
    {
        if (empty($this->getPrivateKey())) {
            return false;
        }
        return true;
    }

    private function validateEnvironment(): bool
    {
        if (!in_array($this->getEnvironment(), Fintecture::AVAILABLE_ENVS)) {
            return false;
        }
        return true;
    }

    private function validateEncryptionDir(): bool
    {
        if (!empty($this->getEncryptionDir())) { // optional parameter
            if (!is_dir($this->getEncryptionDir())) {
                return false;
            }
        }
        return true;
    }
}
