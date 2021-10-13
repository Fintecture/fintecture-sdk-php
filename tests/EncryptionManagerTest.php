<?php

namespace Fintecture\Tests;

use Fintecture\Util\EncryptionManager;
use Fintecture\Util\PemManager;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Error\Error;
use org\bovigo\vfs\vfsStream;

class EncryptionTest extends BaseTest
{
    private $encryptionManager;
    private $pemManager;

    public function setUp()
    {
        parent::setUp();

        // Encryption Manager with encryption key
        $encryptionKey = hex2bin(file_get_contents($this->dataPath . 'fintecture_key_.txt'));
        $this->encryptionManager = new EncryptionManager(vfsStream::url('encryption-keys'));
        $this->encryptionManager->initEncryptionKey($encryptionKey);
        $this->pemManager = new PemManager($this->encryptionManager);
    }

    public function testGenerateEncryptionKey()
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

    public function testInvalidDirectory()
    {
        // Encryption Manager without encryption key and with not existing folder
        $this->expectException(Error::class);
        $encryptionManager = new EncryptionManager(vfsStream::url('bad-dir'));
        $encryptionManager->initEncryptionKey();
    }

    public function testReadingPrivateKey()
    {
        $this->assertEquals($this->privateKey, $this->pemManager->readPrivateKey($this->privateKey));
    }

    public function testReadingPrivateKeyFromFile()
    {
        $this->assertEquals($this->privateKey, $this->pemManager->readPrivateKey($this->privateKeyPath));
    }

    public function testFormattingPrivateKey()
    {
        // $this->expectException(Notice::class);
        $pemResults = $this->pemManager->formatPrivateKey($this->privateKey); // will be encrypted
        $this->assertTrue($pemResults['encrypted']);
    }

    public function testFormattingEncryptedPrivateKey()
    {
        $pemResults = $this->pemManager->formatPrivateKey($this->encryptedPrivateKey); // will be decrypted
        $this->assertEquals($this->privateKey, $pemResults['privateKey']);
    }
}
