<?php

namespace Fintecture;

use Fintecture\Api\ApiFactory;
use Fintecture\Api\ApiResponse;
use Fintecture\Api\Auth\Token;
use Fintecture\Api\Resources\Application;
use Fintecture\Api\Resources\Provider;
use Fintecture\Api\Resources\TestAccount;
use Fintecture\Config\Config;
use Fintecture\Util\EncryptionManager;
use Fintecture\Util\FintectureException;
use Fintecture\Util\PemManager;
use Psr\Http\Client\ClientInterface;

/**
 * @property Application $application
 * @property Provider $provider
 * @property TestAccount $testAccount
 * @property Token $token
 */
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
    * @var Config
    */
    private $config;

    /**
     * @param array $config The config of the user app to create the main client.
     * @param ?ClientInterface $httpClient Client to do HTTP requests, if not set, auto discovery will be used to find a HTTP client.
     */
    public function __construct(array $config, ClientInterface $httpClient = null)
    {
        // Set a unique identifier for the current instance
        $this->identifier .= '-' . uniqid();

        // Configuration
        Fintecture::setCurrentClient($this->identifier);
        $this->config = Fintecture::setConfig($config);

        // Handle encryption for handling of PEM keys
        if ($this->config->getEncryptionDir()) {
            $this->encryptionManager = new EncryptionManager($this->config->getEncryptionDir());
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
        $privateKey = $this->pemManager->readPrivateKey($this->config->getPrivateKey());
        if (!$privateKey) {
            throw new FintectureException('Cannot get private key.');
        }

        $pemResults = $this->pemManager->formatPrivateKey($privateKey);

        $finalPrivateKey = $pemResults['encrypted'] ? $privateKey : $pemResults['privateKey'];
        if ($this->pemManager->isPemString($finalPrivateKey)) {
            $this->config->setFinalPrivateKey($finalPrivateKey);
        } else {
            throw new FintectureException('The private key is not well formatted. Please verify it.');
        }

        if ($this->config->getEncryptionDir()) {
            // Store encrypted private key for developers
            if ($pemResults['encrypted']) {
                $this->config->setEncryptedPrivateKey($pemResults['privateKey']); // newly encrypted key (to save)
            } else {
                $this->config->setEncryptedPrivateKey($privateKey); // original key
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
        return $this->config->getEncryptedPrivateKey();
    }

    /**
     * Get the final used private key in an encryption context.
     *
     * @return ?string Private Key
     */
    public function getFinalPrivateKey(): ?string
    {
        Fintecture::setCurrentClient($this->identifier);
        return $this->config->getFinalPrivateKey();
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
            throw new FintectureException('Invalid target url. Please verify the format.');
        }
    }

    /**
     * Override __get function to call the requested API class.
     *
     * @param string $name Name of the API class.
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        Fintecture::setCurrentClient($this->identifier);

        if (null === $this->apiFactory) {
            try {
                $this->apiFactory = new ApiFactory($this->identifier);
            } catch (\Exception $e) {
                throw new FintectureException($e->getMessage());
            }
        }

        try {
            return $this->apiFactory->__get($name);
        } catch (\Exception $e) {
            throw new FintectureException($e->getMessage());
        }
    }
}
