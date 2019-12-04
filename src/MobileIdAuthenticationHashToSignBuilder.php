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

class MobileIdAuthenticationHashToSignBuilder
{

    /** @var HashType $hashType */
    private $hashType;

    /** @var string $hash */
    private $hash;

    public function getHashType(): ?HashType
    {
        return $this->hashType;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function withHashType(string $hashType): MobileIdAuthenticationHashToSignBuilder
    {
        $this->hashType = MobileIdAuthenticationHashToSign::strToHashType($hashType);

        return $this;
    }

    public function withHashInBase64(string $hash): MobileIdAuthenticationHashToSignBuilder
    {
        $this->hash = $hash;
        return $this;
    }

    function validateFields(): void
    {
        if (is_null($this->getHashType())) {
            throw new MissingOrInvalidParameterException("Missing hash type");
        }

    }

    public function build(): MobileIdAuthenticationHashToSign
    {
        $this->validateFields();
        return new MobileIdAuthenticationHashToSign($this);
    }

}
