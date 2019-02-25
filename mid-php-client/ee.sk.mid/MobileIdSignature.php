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
class MobileIdSignature
{
    private $valueInBase64;

    private $algorithmName;

    public function getValue()
    {
        if (!$this->valueInBase64) {
            throw new InvalidBase64CharacterException("Failed to parse signature value in base64. Probably incorrectly encoded base64 string: '" . $this->valueInBase64 . "'");
        } else {
            return $this->valueInBase64;
        }
    }

    public function __construct($builder)
    {
        $this->valueInBase64 = $builder->getValueInBase64();
        $this->algorithmName = $builder->getAlgorithmName();
    }

    public function getValueInBase64()
    {
        return $this->valueInBase64;
    }

    public function getAlgorithmName()
    {
        return $this->algorithmName;
    }

    public static function newBuilder()
    {
        return new MobileIdSignatureBuilder();
    }

}

class MobileIdSignatureBuilder
{
    private $valueInBase64;
    private $algorithmName;

    public function __construct()
    {
    }

    public function getValueInBase64()
    {
        return $this->valueInBase64;
    }

    public function getAlgorithmName()
    {
        return $this->algorithmName;
    }

    public function withValueInBase64($valueInBase64)
    {
        $this->valueInBase64 = $valueInBase64;
        return $this;
    }

    public function withAlgorithmName($algorithmName)
    {
        $this->algorithmName = $algorithmName;
        return $this;
    }

    public function build()
    {
        return new MobileIdSignature($this);
    }

}
