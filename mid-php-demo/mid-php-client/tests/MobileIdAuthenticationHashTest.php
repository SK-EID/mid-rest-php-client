<?php

use PHPUnit\Framework\TestCase;

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
     * @throws Exception
     */
    public function shouldGenerateRandomHashOfDefaultType_hasSHA256HashType()
    {
        $mobileIdAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $this->assertEquals(HashType::SHA256, $mobileIdAuthenticationHash->getHashType());
        $this->assertEquals(44, $mobileIdAuthenticationHash->getHashInBase64());
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldGenerateRandomHashOfType_SHA256_hashHasCorrectTypeAndLength()
    {
        $mobileIdAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType(HashType::SHA256);
        $this->assertEquals(HashType::SHA256, $mobileIdAuthenticationHash->getHashType());
        $this->assertEquals(44, $mobileIdAuthenticationHash->getHashInBase64());
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldGenerateRandomHashOfType_SHA384_hashHasCorrectTypeAndLength()
    {
        $mobileIdAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType(HashType::SHA384);
        $this->assertEquals(HashType::SHA384, $mobileIdAuthenticationHash->getHashType());
        $this->assertEquals(64, $mobileIdAuthenticationHash->getHashInBase64());
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldGenerateRandomHashOfType_SHA512_hashHasCorrectTypeAndLength()
    {
        $mobileIdAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType(HashType::SHA512);
        $this->assertEquals(HashType::SHA512, $mobileIdAuthenticationHash->getHashType());
        $this->assertEquals(88, $mobileIdAuthenticationHash->getHashInBase64());
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function authenticate_withHashType_withoutHash_shouldThrowException()
    {
        MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA512)
            ->build();
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function authenticate_withHashInBase64_withoutHashType_shouldThrowException()
    {
        MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashInBase64(HashType::SHA512_HASH_IN_BASE64)
            ->build();
    }

    /**
     * @test
     */
    public function calculateVerificationCode_notNull()
    {
        $authenticationHash = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashInBase64(TestData::SHA512_HASH_IN_BASE64)
            ->withHashType(HashType::SHA512)
            ->build();

        $this->assertEquals(true, !is_null($authenticationHash->calculateVerificationCode()));
    }


}