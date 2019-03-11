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
namespace Sk\Mid\HashType;
use Sk\Mid\Util\DigestCalculator;
abstract class HashType
{

    const SHA256 = 'sha256';
    const SHA384 = 'sha384';
    const SHA512 = 'sha512';

    /** @var string $algorithmName */
    private $algorithmName;

    /** @var string $hashTypeName */
    private $hashTypeName;

    /** @var int $lengthInBits */
    private $lengthInBits;

    /** @var array $digestInfoPrefix */
    private $digestInfoPrefix;

    public function __construct(string $algorithmName, string $hashTypeName, int $lengthInBits, array $digestInfoPrefix)
    {
        $this->algorithmName = $algorithmName;
        $this->hashTypeName = $hashTypeName;
        $this->lengthInBits = $lengthInBits;
        $this->digestInfoPrefix = $digestInfoPrefix;
    }

    public function getAlgorithmName(): string
    {
        return $this->algorithmName;
    }

    public function getHashTypeName(): string
    {
        return $this->hashTypeName;
    }

    public function getLengthInBits(): int
    {
        return $this->lengthInBits;
    }

    public function getDigestInfoPrefix(): array
    {
        return $this->digestInfoPrefix;
    }

    public function calculateDigest(string $dataToDigest ) : string
    {
        return DigestCalculator::calculateDigest($dataToDigest, $this->getHashTypeName());
    }

    public function getLengthInBytes() : int {
        return $this->lengthInBits / 8;
    }

}
