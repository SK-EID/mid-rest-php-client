<?php
namespace Sk\Mid\Tests;
use Sk\Mid\VerificationCodeCalculator;
use Sk\Mid\HashType\HashType;
use Sk\Mid\Util\DigestCalculator;

use PHPUnit\Framework\TestCase;

final class VerificationCodeCalculatorTest extends TestCase
{
    const HACKERMAN_SHA256 = "HACKERMAN_SHA256";
    const HACKERMAN_SHA384 = "HACKERMAN_SHA384";
    const HACKERMAN_SHA512 = "HACKERMAN_SHA512";

    /** @test
     */
    public function calculateVerificationCode_verifyExampleMidInDocumentation()
    {
        $hash = '2f665f6a6999e0ef0752e00ec9f453adf59d8cb6';
        $verificationCode = $this->calculateVerificationCode($hash);
        $this->assertEquals('1462', $verificationCode);
    }

    /**
     * @test
     */
    public function calculateVerificationCode_calculateVerificationCode_withSHA256()
    {
        $verificationCode = $this->calculateVerificationCode($this->getStringDigest(self::HACKERMAN_SHA256, HashType::SHA256));
        $this->assertEquals('6008', $verificationCode);
    }

    /**
     * @test
     */
    public function calculateVerificationCode_withSHA384()
    {
        $verificationCode = $this->calculateVerificationCode($this->getStringDigest(self::HACKERMAN_SHA384, HashType::SHA384));
        $this->assertEquals('7230', $verificationCode);
    }

    /**
     * @test
     */
    public function calculateVerificationCode_withSHA512()
    {
        $verificationCode = $this->calculateVerificationCode($this->getStringDigest(self::HACKERMAN_SHA512, HashType::SHA512));
        $this->assertEquals('3843', $verificationCode);
    }

    /**
     * @test
     */
    public function calculateVerificationCode_withTooShortHash()
    {
        $verificationCode = $this->calculateVerificationCode("1001000110100");
        $this->assertEquals('0000', $verificationCode);
    }

    /**
     * @test
     */
    public function calculateVerificationCode_withNullHash()
    {
        $verificationCode = $this->calculateVerificationCode(null);
        $this->assertEquals('0000', $verificationCode);
    }

    private function calculateVerificationCode($dummyDocumentHash)
    {
        return VerificationCodeCalculator::calculateMobileIdVerificationCode($dummyDocumentHash);
    }

    private function getStringDigest($hash, $hashType)
    {
        return DigestCalculator::calculateDigest($hash, $hashType);
    }

}
