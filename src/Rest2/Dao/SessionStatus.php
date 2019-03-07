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
namespace Sk\Mid\Rest\Dao;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\MobileIdSignature;

class SessionStatus
{
    /** @var string $state */
    private $state;

    /** @var string $result */
    private $result;

    /** @var MobileIdSignature $signature */
    private $signature;

    /** @var string $cert */
    private $cert;

    public function __construct(array $values = array())
    {
        if (isset($values['state'])) {
            $this->state = $values['state'];
        }
        if (isset($values['result'])) {
            $this->result = $values['result'];
        }
        if (isset($values['cert'])) {
            $this->cert = $values['cert'];
        }
        if (isset($values['signature'])) {
            $this->signature = MobileIdSignature::newBuilder()
                ->withAlgorithmName($values['signature']['algorithm'])
                ->withValueInBase64($values['signature']['value'])
                ->build();
        }

    }

    public function getState() : ?string
    {
        return $this->state;
    }

    public function setState(string $state)
    {
        $this->state = $state;
    }

    public function getResult() : ?string
    {
        return $this->result;
    }

    public function setResult(?string $result)
    {
        $this->result = $result;
    }

    public function getSignature() : MobileIdSignature
    {
        return $this->signature;
    }

    public function setSignature(MobileIdSignature $signature)
    {
        $this->signature = $signature;
    }

    public function getCert() : string
    {
        if (empty($this->cert) || is_null($this->cert)) {
            throw new MissingOrInvalidParameterException("Certificate must be set.");
        }
        return $this->cert;
    }

    public function setCert(?string $cert)
    {
        $this->cert = $cert;
    }

    public function isComplete() : bool {
        return strcasecmp("COMPLETE", $this->getState()) == 0;
    }

    public function toString() : string
    {
        return "SessionStatus{<br/>state=".$this->state.",<br/> result=".$this->result.",<br/> signature=".$this->signature->getValue().", <br/>cert=".$this->cert."<br/>}<br/><br/>";
    }


}
