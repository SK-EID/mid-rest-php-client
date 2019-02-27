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
class SessionStatus
{
    private $state;
    private $result;
    private $signature;
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

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    public function getCert()
    {
        return $this->cert;
    }

    public function setCert($cert)
    {
        $this->cert = $cert;
    }

    public function isComplete() {
        return strcasecmp("COMPLETE", $this->getState()) == 0;
    }

    public function toString()
    {
        return "SessionStatus{<br/>state=".$this->state.",<br/> result=".$this->result.",<br/> signature=".$this->signature.", <br/>cert=".$this->cert."<br/>}<br/><br/>";
    }


}