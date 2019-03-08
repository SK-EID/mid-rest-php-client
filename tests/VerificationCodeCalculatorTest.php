<?php
namespace Sk\Mid\Tests;
use Sk\Mid\VerificationCodeCalculator;
use Sk\Mid\HashType\HashType;
use Sk\Mid\Util\DigestCalculator;

use PHPUnit\Framework\TestCase;
use TypeError;

final class VerificationCodeCalculatorTest extends TestCase
{
    const HACKERMAN_SHA256 = "HACKERMAN_SHA256";
    const HACKERMAN_SHA384 = "HACKERMAN_SHA384";
    const HACKERMAN_SHA512 = "HACKERMAN_SHA512";

    /** @test
     * @throws \Exception
     */
    public function calculateVerificationCode_verifyExampleMidInDocumentation()
    {
        $hash = '2f665f6a6999e0ef0752e00ec9f453adf59d8cb6';
        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode($hash);
        $this->assertEquals('1462', $verificationCode);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function calculateVerificationCode_calculateVerificationCode_withSHA256()
    {
        $hash = DigestCalculator::calculateDigest(self::HACKERMAN_SHA256, HashType::SHA256);
        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode($hash);
        $this->assertEquals('6008', $verificationCode);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function calculateVerificationCode_withSHA384()
    {
        $hash = DigestCalculator::calculateDigest(self::HACKERMAN_SHA384, HashType::SHA384);
        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode($hash);
        $this->assertEquals('7230', $verificationCode);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function calculateVerificationCode_withSHA512()
    {
        $hash = DigestCalculator::calculateDigest(self::HACKERMAN_SHA512, HashType::SHA512);
        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode($hash);
        $this->assertEquals('3843', $verificationCode);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function calculateVerificationCode_withTooShortHash()
    {
        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode("1001000110100");
        $this->assertEquals('0000', $verificationCode);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function calculateVerificationCode_withNullHash_shouldThrowTypeError()
    {
        $this->expectException(TypeError::class);

        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode(null);
        $this->assertEquals('0000', $verificationCode);
    }

}
