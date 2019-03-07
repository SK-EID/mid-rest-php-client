<?php
namespace Sk\Mid\Tests;
use PHPUnit\Framework\TestCase;
use Sk\Mid\Hashtype\HashType;
use Sk\Mid\MobileIdAuthenticationHashToSign;
use Sk\Mid\Hashtype\Sha256;
use Sk\Mid\Hashtype\Sha384;
use Sk\Mid\Hashtype\Sha512;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 5:27 PM
 */
class MobileIdAuthenticationHashTest extends TestCase
{

    /**
     * @test
     */
    public function shouldGenerateRandomHashOfDefaultType_hasSHA256HashType()
    {
        $mobileIdAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $this->assertEquals(new Sha256(), $mobileIdAuthenticationHash->getHashType());
        $this->assertEquals(44, strlen($mobileIdAuthenticationHash->getHashInBase64()));
    }

    /**
     * @test
     */
    public function shouldGenerateDefaultHashOfType_SHA256_hashHasCorrectTypeAndLength()
    {
        $mobileIdAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $this->assertEquals(new Sha256(), $mobileIdAuthenticationHash->getHashType());
        $this->assertEquals(44, strlen($mobileIdAuthenticationHash->getHashInBase64()));
    }

    /**
     * @test
     */
    public function shouldGenerateRandomHashOfType_SHA256_hashHasCorrectTypeAndLength()
    {
        $mobileIdAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfType(HashType::SHA256);
        $this->assertEquals(new Sha256(), $mobileIdAuthenticationHash->getHashType());
        $this->assertEquals(44, strlen($mobileIdAuthenticationHash->getHashInBase64()));
    }

    /**
     * @test
     */
    public function shouldGenerateRandomHashOfType_SHA384_hashHasCorrectTypeAndLength()
    {
        $mobileIdAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfType(HashType::SHA384);
        $this->assertEquals(new Sha384(), $mobileIdAuthenticationHash->getHashType());
        $this->assertEquals(64, strlen($mobileIdAuthenticationHash->getHashInBase64()));
    }

    /**
     * @test
     */
    public function shouldGenerateRandomHashOfType_SHA512_hashHasCorrectTypeAndLength()
    {
        $mobileIdAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfType(HashType::SHA512);
        $this->assertEquals(new Sha512(), $mobileIdAuthenticationHash->getHashType());
        $this->assertEquals(88, strlen($mobileIdAuthenticationHash->getHashInBase64()));
    }


    /**
     * @test
     */
    public function calculateVerificationCode_notNull()
    {
        $authenticationHash = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA512)
            ->build();

        $this->assertEquals(true, !is_null($authenticationHash->calculateVerificationCode()));
    }


}
