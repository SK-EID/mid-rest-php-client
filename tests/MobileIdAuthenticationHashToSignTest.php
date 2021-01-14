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
use PHPUnit\Framework\TestCase;
use Sk\Mid\HashType\HashType;
use Sk\Mid\MobileIdAuthenticationHashToSign;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\VerificationCodeCalculator;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

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
