<?php
namespace Sk\Mid\Tests;
use PHPUnit\Framework\TestCase;
use Sk\Mid\HashType\HashType;
use Sk\Mid\MobileIdAuthenticationHashToSign;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\VerificationCodeCalculator;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
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
     */
    public function setHashInBase64_calculateVerificationCode_withSHA384()
    {
        $MobileIdAuthenticationHashToSign = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA384)
            ->build();

        $this->assertEquals(4, strlen($MobileIdAuthenticationHashToSign->calculateVerificationCode()));
    }

    /**
     * @test
     */
    public function setHashType_incorrectType_shouldThrowException() {
        $this->expectException(MissingOrInvalidParameterException::class);

        MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType("sha123")
            ->build();
    }

    /**
     * @test
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
     */
    public function checkFields_withoutHashType()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        MobileIdAuthenticationHashToSign::newBuilder()
            ->build();
    }

    /**
     * @test
     */
    public function correctHashToSign() {
        $hashToSign = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA256)
            ->withHashInBase64(TestData::SHA256_HASH_IN_BASE64)
            ->build();
        self::assertEquals(true, !is_null($hashToSign));
    }



}
