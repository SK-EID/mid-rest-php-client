<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2019 SK ID Solutions AS
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

use phpDocumentor\Reflection\Types\This;


require_once 'HashType.php';
class MobileIdAuthenticationHashToSign
{
    const DEFAULT_HASH_TYPE = HashType::SHA256;


    private $hash;
    private $hashType;


    public function __construct($builder)
    {
        $this->hashType = $builder->getHashType();
        $this->hash = openssl_random_pseudo_bytes($builder->getHashType()->getLengthInBytes());
    }


    public function getHashInBase64()
    {
        return base64_encode($this->hash);
    }

    public function getHashType()
    {
        return $this->hashType;
    }

    public function calculateVerificationCode()
    {
        return VerificationCodeCalculator::calculateMobileIdVerificationCode($this->hash);
    }

    public static function generateRandomHashOfDefaultType()
    {
        return self::generateRandomHashOfType(self::DEFAULT_HASH_TYPE);
    }

    public static function generateRandomHashOfType($hashTypeName)
    {
        if ($hashTypeName == 'sha256') {
            $hashType = new Sha256();
        }
        if ($hashTypeName == 'sha384') {
            $hashType = new Sha384();
        }
        if ($hashTypeName == 'sha512') {
            $hashType = new Sha512();
        }

        return MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType($hashType)
            ->build();
    }

    public static function newBuilder()
    {
        return new MobileIdAuthenticationHashToSignBuilder();
    }

}

class MobileIdAuthenticationHashToSignBuilder
{

    private $hashType;

    public function withHashType($hashType)
    {
        $this->hashType = $hashType;
        return $this;
    }

    function validateFields()
    {
        if (is_null($this->hashType))
        {
            throw new ParameterMissingException("Missing hash type");
        }

    }

    public function build()
    {
        $this->validateFields();
        return new MobileIdAuthenticationHashToSign($this);
    }
}
