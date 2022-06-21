<?php

namespace Fintecture\Tests;

use Fintecture\Util\EncryptionManager;
use Fintecture\Util\PemManager;
use org\bovigo\vfs\vfsStream;

class EncryptionTest extends BaseTest
{
    /** @var EncryptionManager $encryptionManager */
    private $encryptionManager;

    /** @var PemManager $pemManager */
    private $pemManager;

    public function setUp(): void
    {
        parent::setUp();

        // Encryption Manager with encryption key
        $encryptionKey = hex2bin(file_get_contents($this->dataPath . 'fintecture_key_.txt'));
        $this->encryptionManager = new EncryptionManager(vfsStream::url('encryption-keys'));
        $this->encryptionManager->initEncryptionKey($encryptionKey);
        $this->pemManager = new PemManager($this->encryptionManager);
    }

    public function testGenerateEncryptionKey(): void
    {
        // Generate encryption key
        $encryptionManager = new EncryptionManager(vfsStream::url('encryption-keys'));
        $this->assertTrue($encryptionManager->initEncryptionKey());

        // The same when there are already two files
        $hex = 'c18077ee1950744409d2b2f82ffab8bc';
        vfsStream::newFile('fintecture_key_1a187eb9461644d4.txt')->at($this->root)->setContent($hex);
        vfsStream::newFile('fintecture_key_d47ba114468e6194.txt')->at($this->root)->setContent($hex);
        $encryptionManager = new EncryptionManager(vfsStream::url('encryption-keys'));
        $this->assertTrue($encryptionManager->initEncryptionKey());
    }

    public function testInvalidDirectory(): void
    {
        // Encryption Manager without encryption key and with not existing folder
        $this->expectError();
        $encryptionManager = new EncryptionManager(vfsStream::url('bad-dir'));
        $encryptionManager->initEncryptionKey();
    }

    public function testReadingPrivateKey(): void
    {
        $this->assertEquals($this->privateKey, $this->pemManager->readPrivateKey($this->privateKey));
    }

    public function testReadingPrivateKeyFromFile(): void
    {
        $this->assertEquals($this->privateKey, $this->pemManager->readPrivateKey($this->privateKeyPath));
    }

    public function testFormattingPrivateKey(): void
    {
        $pemResults = $this->pemManager->formatPrivateKey($this->privateKey); // will be encrypted
        $this->assertTrue($pemResults['encrypted']);
    }

    public function testFormattingEncryptedPrivateKey(): void
    {
        $pemResults = $this->pemManager->formatPrivateKey($this->encryptedPrivateKey); // will be decrypted
        $this->assertEquals($this->privateKey, $pemResults['privateKey']);
    }
}
