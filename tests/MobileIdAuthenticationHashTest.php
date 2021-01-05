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
use Sk\Mid\HashType\Sha256;
use Sk\Mid\HashType\Sha384;
use Sk\Mid\HashType\Sha512;

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

    /**
     * @test
     */
    public function calculateVerificationCode_ensureVerificationCodesAreNotAlwaysEqual() {
        $distinctCodes = array();

        for ($i=0; $i < 5; $i++) {
            $verificationCode = MobileIdAuthenticationHashToSign::generateRandomHashOfType(HashType::SHA512)->calculateVerificationCode();

            $distinctCodes[''.$verificationCode] = $i;
        }

        $differentKeysCount = sizeof($distinctCodes);

        $this->assertThat($differentKeysCount, $this->greaterThan(1),
                'Generated 5 hashes, calculated verification codes and every time ended up with '.array_keys($distinctCodes)[0]);
    }


}
