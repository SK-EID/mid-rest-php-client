<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2021 SK ID Solutions AS
 * %%
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * #L%
 */
namespace Sk\Mid\Tests;
use InvalidArgumentException;
use Sk\Mid\VerificationCodeCalculator;
use Sk\Mid\HashType\HashType;
use Sk\Mid\Util\DigestCalculator;

use PHPUnit\Framework\TestCase;
use TypeError;

final class VerificationCodeCalculatorTest extends TestCase
{

    /** @test
     */
    public function calculateVerificationCode_verifyExampleMidInDocumentation()
    {
        $hash = '2f665f6a6999e0ef0752e00ec9f453adf59d8cb6';
        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode($hash);
        $this->assertEquals('1462', $verificationCode);
    }

    /**
     * @test
     */
    public function calculateVerificationCode_calculateVerificationCode_withSHA256()
    {
        $hash = DigestCalculator::calculateDigest("HACKERMAN_SHA256", HashType::SHA256);
        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode($hash);
        $this->assertEquals('6008', $verificationCode);
    }

    /**
     * @test
     */
    public function calculateVerificationCode_withSHA384()
    {
        $hash = DigestCalculator::calculateDigest("HACKERMAN_SHA384", HashType::SHA384);
        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode($hash);
        $this->assertEquals('7230', $verificationCode);
    }

    /**
     * @test
     */
    public function calculateVerificationCode_withSHA512()
    {
        $hash = DigestCalculator::calculateDigest("HACKERMAN_SHA512", HashType::SHA512);
        $verificationCode = VerificationCodeCalculator::calculateMobileIdVerificationCode($hash);
        $this->assertEquals('3843', $verificationCode);
    }

    /**
     * @test
     */
    public function calculateVerificationCode_withTooShortHash()
    {
        $this->expectException(InvalidArgumentException::class);
        VerificationCodeCalculator::calculateMobileIdVerificationCode("1001000110100");
    }

    /**
     * @test
     */
    public function calculateVerificationCode_withNullHash_shouldThrowTypeError()
    {
        $this->expectException(TypeError::class);

        VerificationCodeCalculator::calculateMobileIdVerificationCode(null);
    }

}
