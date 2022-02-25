<?php

namespace Fintecture;

use Fintecture\Api\ApiFactory;
use Fintecture\Api\ApiResponse;
use Fintecture\Util\EncryptionManager;
use Fintecture\Util\PemManager;
use Http\Client\HttpClient;

abstract class Client
{
    /**
     * @var string Identifier of the client.
     */
    protected $identifier = 'base';

    /**
     * @var ApiFactory
     */
    private $apiFactory;

    /**
     * @var EncryptionManager
     */
    private $encryptionManager;

    /**
     * @var PemManager
     */
    private $pemManager;

    /**
     * @param array $config The config of the user app to create the main client.
     * @param ?HttpClient $httpClient Client to do HTTP requests, if not set, auto discovery will be used to find a HTTP client.
     */
    public function __construct(array $config, HttpClient $httpClient = null)
    {
        // Set a unique identifier for the current instance
        $this->identifier .= '-' . uniqid();

        // Configuration
        Fintecture::setCurrentClient($this->identifier);
        Fintecture::setConfig($config);

        // Handle encryption for handling of PEM keys
        if (Fintecture::getConfig()->getEncryptionDir()) {
            $this->encryptionManager = new EncryptionManager(Fintecture::getConfig()->getEncryptionDir());
            $this->encryptionManager->initEncryptionKey();
        }
        $this->pemManager = new PemManager($this->encryptionManager);
        $this->initPrivateKey();

        // Last initializations
        Fintecture::setHttpClient($httpClient);
        Fintecture::setRequestFactory();
        Fintecture::setStreamFactory();
        Fintecture::setApiWrapper();
    }

    /**
     * Initialization of private key
     *
     * @throws \Exception if the final private key is not well formatted.
     */
    private function initPrivateKey(): void
    {
        // Private Key handling / config updates
        $privateKey = $this->pemManager->readPrivateKey(Fintecture::getConfig()->getPrivateKey());
        $pemResults = $this->pemManager->formatPrivateKey($privateKey);

        $finalPrivateKey = $pemResults['encrypted'] ? $privateKey : $pemResults['privateKey'];
        if ($this->pemManager->isPemString($finalPrivateKey)) {
            Fintecture::getConfig()->setFinalPrivateKey($finalPrivateKey);
        } else {
            throw new \Exception('The private key is not well formatted. Please verify it.');
        }

        if (Fintecture::getConfig()->getEncryptionDir()) {
            // Store encrypted private key for developers
            if ($pemResults['encrypted']) {
                Fintecture::getConfig()->setEncryptedPrivateKey($pemResults['privateKey']); // newly encrypted key (to save)
            } else {
                Fintecture::getConfig()->setEncryptedPrivateKey($privateKey); // original key
            }
        }
    }

    /**
     * Get the generated encrypted Private Key for storage reasons in an encryption context.
     *
     * @return ?string Private Key
     */
    public function getEncryptedPrivateKey(): ?string
    {
        Fintecture::setCurrentClient($this->identifier);
        return Fintecture::getConfig()->getEncryptedPrivateKey();
    }

    /**
     * Get the final used private key in an encryption context.
     *
     * @return ?string Private Key
     */
    public function getFinalPrivateKey(): ?string
    {
        Fintecture::setCurrentClient($this->identifier);
        return Fintecture::getConfig()->getFinalPrivateKey();
    }

    /**
     * Set the access token.
     *
     * @param ApiResponse $accessToken Access Token
     */
    public function setAccessToken(ApiResponse $accessToken): void
    {
        Fintecture::setCurrentClient($this->identifier);
        Fintecture::setAccessToken($accessToken);
    }

    /**
     * Redirect to the given url.
     *
     * @param string $url Target url.
     *
     * @throws \Exception if url is invalid.
     */
    public function redirect(string $url): void
    {
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            header('Location: ' . $url);
            exit;
        } else {
            throw new \Exception('Invalid target url. Please verify the format.');
        }
    }

    /**
     * Override __get function to call the requested API class.
     *
     * @param string $name Name of the API class.
     */
    public function __get(string $name)
    {
        Fintecture::setCurrentClient($this->identifier);

        if (null === $this->apiFactory) {
            try {
                $this->apiFactory = new ApiFactory($this->identifier);
            } catch (\Exception $e) {
                \trigger_error($e->getMessage(), E_USER_ERROR);
            }
        }

        try {
            return $this->apiFactory->__get($name);
        } catch (\Exception $e) {
            \trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }
}
