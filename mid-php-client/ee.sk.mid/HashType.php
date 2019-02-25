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
require_once __DIR__ . '/util/DigestCalculator.php';
abstract class HashType
{

    const SHA256 = 'sha256';
    const SHA384 = 'sha384';
    const SHA512 = 'sha512';

    private $algorithmName;
    private $hashTypeName;
    private $lengthInBits;
    private $digestInfoPrefix;

    public function __construct($algorithmName, $hashTypeName, $lengthInBits, $digestInfoPrefix)
    {
        $this->algorithmName = $algorithmName;
        $this->hashTypeName = $hashTypeName;
        $this->lengthInBits = $lengthInBits;
        $this->digestInfoPrefix = $digestInfoPrefix;
    }

    public function getAlgorithmName()
    {
        return $this->algorithmName;
    }

    public function getHashTypeName()
    {
        return $this->hashTypeName;
    }

    public function getLengthInBytes()
    {
        return $this->lengthInBits / 8;
    }

    public function getDigestInfoPrefix()
    {
        return $this->digestInfoPrefix;
    }

    public function calculateDigest( $dataToDigest )
    {
        return DigestCalculator::calculateDigest($dataToDigest, $this->getHashTypeName());
    }

}

class Sha256 extends HashType
{
    public function __construct()
    {
        parent::__construct("SHA-256", "SHA256", 256, array(48, 49, 48, 13, 6, 9, 96, -122, 72, 1, 101, 3, 4, 2, 1, 5, 0, 4, 32));
    }
}

class Sha384 extends HashType
{
    public function __construct()
    {
        parent::__construct("SHA-384", "SHA384", 384, array(48, 65, 48, 13, 6, 9, 96, -122, 72, 1, 101, 3, 4, 2, 2, 5, 0, 4, 48));
    }
}

class Sha512 extends HashType
{

    public function __construct()
    {
        parent::__construct("SHA-512", "SHA512", 512, array(48, 81, 48, 13, 6, 9, 96, -122, 72, 1, 101, 3, 4, 2, 3, 5, 0, 4, 64));
    }

}
