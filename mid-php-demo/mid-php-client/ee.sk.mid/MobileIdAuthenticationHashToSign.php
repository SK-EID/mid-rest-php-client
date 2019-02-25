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

require_once 'HashToSign.php';
require_once 'HashType.php';
class MobileIdAuthenticationHashToSign extends HashToSign
{
    const DEFAULT_HASH_TYPE = HashType::SHA256;

    public function __construct($builder)
    {
        parent::__construct($builder);
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

        $dataToHash = self::getRandomBytes($hashType->getLengthInBytes());
        echo 'datatohash: '.$dataToHash;
        return MobileIdAuthenticationHashToSign::newBuilder()
            ->withDataToHash($dataToHash)
            ->withHash(DigestCalculator::calculateDigest($dataToHash, $hashType))
            ->withHashType($hashType)
            ->build();
    }

    public static function newBuilder()
    {
        return new MobileIdAuthenticationHashToSignBuilder();
    }

    private static function getRandomBytes($lengthInBytes)
    {
        return openssl_random_pseudo_bytes($lengthInBytes);
    }
}

class MobileIdAuthenticationHashToSignBuilder extends HashToSignBuilder
{
    public function withDataToHash($dataToHash)
    {
        parent::withDataToHash($dataToHash);
        return $this;
    }

    public function withHash($hash)
    {
        parent::withHash($hash);
        return $this;
    }

    public function withHashInBase64($hashInBase64)
    {
        parent::withHashInBase64($hashInBase64);
        return $this;
    }

    public function withHashType($hashType)
    {
        parent::withHashType($hashType);
        return $this;
    }

    public function build()
    {
        $this->validateFields();
        return new MobileIdAuthenticationHashToSign($this);
    }
}
