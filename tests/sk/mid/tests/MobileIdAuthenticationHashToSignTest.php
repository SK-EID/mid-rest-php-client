<?php
namespace sk\mid\tests;

use Exception;
use PHPUnit\Framework\TestCase;
use sk\mid\HashType;
use sk\mid\MobileIdAuthenticationHashToSign;
use sk\mid\tests\mock\TestData;
use sk\mid\VerificationCodeCalculator;
use sk\mid\exception\MissingOrInvalidParameterException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 5:03 PM
 */
class MobileIdAuthenticationHashToSignTest extends TestCase
{


    /**
     * @test
     * @throws Exception
     */
    public function setHashInBase64_calculateVerificationCode_withSHA384()
    {
        $MobileIdAuthenticationHashToSign = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA384)
            ->build();

        // TODO asser

        $this->assertEquals(4, strlen($MobileIdAuthenticationHashToSign->calculateVerificationCode()));
    }

    /**
     * @test
     * @expectedException InvalidParameterException
     */
    public function setHashType_incorrectType_shouldThrowException() {
        MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType("sha123")
            ->build();
    }

    /**
     * @test
     * @throws Exception
     */
    public function setHashInBase64_calculateVerificationCode_withSHA512()
    {
        $MobileIdAuthenticationHashToSign = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA512)
            ->build();
        $this->assertEquals(4, strlen($MobileIdAuthenticationHashToSign->calculateVerificationCode()));
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function checkFields_withoutHashType()
    {
        MobileIdAuthenticationHashToSign::newBuilder()
            ->build();
    }

    /**
     * @test
     * @throws Exception
     */
    public function correctHashToSign() {
        $hashToSign = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA256)
            ->withHashInBase64(TestData::SHA256_HASH_IN_BASE64)
            ->build();
        self::assertEquals(true, !is_null($hashToSign));
    }



}
