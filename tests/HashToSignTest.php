<?php

use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 5:03 PM
 */
class HashToSignTest extends TestCase
{

    /**
     * @test
     * @expectedException InvalidBase64CharacterException
     */
    public function setInvalidHashInBase64_shouldThrowException()
    {
        $hashToSign = HashToSign::newBuilder()
            ->withHashInBase64("!IsNotValidBase64String")
            ->withHashType(HashType::SHA256)
            ->build();
    }

    /**
     * @test
     * @throws Exception
     */
    public function setHashInBase64_calculateVerificationCode_withSHA384()
    {
        $hashToSign = HashToSign::newBuilder()
            ->withHashInBase64(TestData::SHA384_HASH_IN_BASE64)
            ->withHashType(HashType::SHA384)
            ->build();
        $this->assertEquals("5781", $hashToSign->calculateVerificationCode());
    }

    /**
     * @test
     * @throws Exception
     */
    public function setHashInBase64_calculateVerificationCode_withSHA512()
    {
        $hashToSign = HashToSign::newBuilder()
            ->withHashInBase64(TestData::SHA512_HASH_IN_BASE64)
            ->withHashType(HashType::SHA512)
            ->build();
        $this->assertEquals("4667", $hashToSign->calculateVerificationCode());
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function checkFields_withoutHashType()
    {
        HashToSign::newBuilder()
            ->withHashInBase64(TestData::SHA512_HASH_IN_BASE64)
            ->build();
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function checkFields_withoutHash()
    {
        HashToSign::newBuilder()
            ->withHashType(HashType::SHA512)
            ->build();
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function checkFields_withoutHash_withoutHashType_withoutData()
    {
        HashToSign::newBuilder()
            ->build();
    }


}