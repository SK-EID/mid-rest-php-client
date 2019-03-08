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
namespace Sk\Mid;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\HashType\HashType;
use Sk\Mid\HashType\Sha256;
use Sk\Mid\HashType\Sha384;
use Sk\Mid\HashType\Sha512;

class MobileIdAuthenticationHashToSign
{
    const DEFAULT_HASH_TYPE = HashType::SHA256;

    /** @var string $hash */
    private $hash;

    /** @var HashType $hashType */
    private $hashType;


    public function __construct(MobileIdAuthenticationHashToSignBuilder $builder)
    {
        $this->hashType = $builder->getHashType();

        if (null !== $builder->getHash()) {
            $this->hash = $builder->getHash();
        }
        else {
            $this->hash = openssl_random_pseudo_bytes($builder->getHashType()->getLengthInBytes());
        }

    }

    public function getHashInBase64() : string
    {
        return base64_encode($this->hash);
    }

    public function getHashType() : HashType
    {
        return $this->hashType;
    }

    public function calculateVerificationCode() : string
    {
        return VerificationCodeCalculator::calculateMobileIdVerificationCode(bin2hex($this->hash));
    }

    public static function generateRandomHashOfDefaultType() : MobileIdAuthenticationHashToSign
    {

        return MobileIdAuthenticationHashToSign::newBuilder()
                ->withHashType(self::DEFAULT_HASH_TYPE)
                ->build();

    }

    public static function generateRandomHashOfType(string $hashTypeName) : MobileIdAuthenticationHashToSign
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
            ->withHashType(strtolower($hashType->getHashTypeName()))
            ->build();
    }

    public static function newBuilder() : MobileIdAuthenticationHashToSignBuilder
    {
        return new MobileIdAuthenticationHashToSignBuilder();
    }

    public static function strToHashType($hashTypeStr) : HashType {

        switch ($hashTypeStr) {
            case 'sha256':
                return new Sha256();
            case  'sha384':
                return new Sha384();
            case 'sha512':
                return new Sha512();
        }
        throw new MissingOrInvalidParameterException("Unknown hash type " . $hashTypeStr);

    }

}

class MobileIdAuthenticationHashToSignBuilder
{

    /** @var HashType $hashType */
    private $hashType;

    /** @var string $hash */
    private $hash;

    public function getHashType() : ?HashType
    {
        return $this->hashType;
    }

    public function getHash() : ?string
    {
        return $this->hash;
    }

    public function withHashType(string $hashType) : MobileIdAuthenticationHashToSignBuilder
    {
        $this->hashType = MobileIdAuthenticationHashToSign::strToHashType($hashType);

        return $this;
    }

    public function withHashInBase64(string $hash) : MobileIdAuthenticationHashToSignBuilder
    {
        $this->hash = $hash;
        return $this;
    }


    function validateFields() : void
    {
        if (is_null($this->getHashType()))
        {
            throw new MissingOrInvalidParameterException("Missing hash type");
        }

    }

    public function build() : MobileIdAuthenticationHashToSign
    {
        $this->validateFields();
        return new MobileIdAuthenticationHashToSign($this);
    }
}
