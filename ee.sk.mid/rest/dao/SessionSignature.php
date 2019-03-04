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
class SessionSignature
{
    /** @var string $algorithm */
    private $algorithm;

    /** @var string $value */
    private $value;

    public function __construct()
    {
    }

    public function getAlgorithm() : string
    {
        return $this->algorithm;
    }

    public function setAlgorithm(string $algorithm) : void
    {
        $this->algorithm = $algorithm;
    }

    public function getValue() : string
    {
        return $this->value;
    }

    public function setValue(string $value) : void
    {
        $this->value = $value;
    }

    public function toString() : string
    {
        return "SessionSignature{<br/>algorithm=".$this->algorithm.",<br/>value=".$this->value."<br/>}<br/><br/>";
    }

}
