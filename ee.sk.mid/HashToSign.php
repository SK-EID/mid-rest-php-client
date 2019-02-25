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
require_once __DIR__ . '/exception/ParameterMissingException.php';
require_once __DIR__ . '/exception/InvalidBase64CharacterException.php';
require_once 'VerificationCodeCalculator.php';
class HashToSign
{
    private $hash;
    private $hashType;

    public function __construct($builder)
    {
        $this->hashType = $builder->getHashType();
        if (!is_null($builder->getDataToHash())) {
            $this->hash = $builder->getHashType()->calculateDigest($builder->getDataToHash());
        } else {
            $this->hash = $builder->getHash();
        }
    }

    public function getHash()
    {
        return $this->hash;
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

    public static function newBuilder()
    {
        return new HashToSignBuilder();
    }
}

class HashToSignBuilder
{

    private $hash;
    private $hashType;
    private $dataToHash;

    public function withDataToHash($dataToHash)
    {
        if (is_null($dataToHash) || strlen($dataToHash) == 0) {
            throw new ParameterMissingException("Cannot pass empty dataToHash value");
        }
        $this->dataToHash = $dataToHash;
        return $this;
    }

    public function withHash($hash)
    {
        if (is_null($hash) || strlen($hash) == 0) {
            throw new ParameterMissingException("Cannot pass empty hash value");
        }
        $this->hash = $hash;
        return $this;
    }

    public function withHashInBase64($hashInBase64)
    {
        if (base64_encode(base64_decode($hashInBase64)) === $hashInBase64) {
            $this->hash = base64_decode($hashInBase64);
        } else {
            throw new InvalidBase64CharacterException();
        }
        return $this;
    }

    public function withHashType($hashType)
    {
        $this->hashType = $hashType;
        return $this;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getHashType()
    {
        return $this->hashType;
    }

    public function getDataToHash()
    {
        return $this->dataToHash;
    }

    public function build()
    {
        $this->validateFields();
        return new HashToSign($this);
    }

    function validateFields()
    {
        if (is_null($this->hashType))
        {
            throw new ParameterMissingException("Missing hash type");
        }
        if (is_null($this->hash) && is_null($this->dataToHash))
        {
            throw new ParameterMissingException("Missing hash or dataToHash");
        }
        if (!is_null($this->hash) && !is_null($this->dataToHash))
        {
            throw new ParameterMissingException("You can only pass in either hash or dataToHash but not both");
        }
    }
}
