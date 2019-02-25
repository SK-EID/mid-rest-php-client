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
require_once 'AbstractAuthSignRequestBuilder.php';
class AuthenticationRequestBuilder extends AbstractAuthSignRequestBuilder
{

    public function __construct()
    {
        parent::__construct();
    }

    public function withRelyingPartyUUID($relyingPartyUUID)
    {
        parent::withRelyingPartyUUID($relyingPartyUUID);
        return $this;
    }

    public function withRelyingPartyName($relyingPartyName)
    {
        parent::withRelyingPartyName($relyingPartyName);
        return $this;
    }

    public function withPhoneNumber($phoneNumber)
    {
        parent::withPhoneNumber($phoneNumber);
        return $this;
    }

    public function withNationalIdentityNumber($nationalIdentityNumber)
    {
        parent::withNationalIdentityNumber($nationalIdentityNumber);
        return $this;
    }

    public function withHashToSign($mobileIdAuthenticationHash)
    {
        parent::withHashToSign($mobileIdAuthenticationHash);
        return $this;
    }

    public function withLanguage($language)
    {
        parent::withLanguage($language);
        return $this;
    }

    public function withDisplayText($displayText)
    {
        parent::withDisplayText($displayText);
        return $this;
    }

    public function withDisplayTextFormat($displayTextFormat)
    {
        parent::withDisplayTextFormat($displayTextFormat);
        return $this;
    }

    public function build()
    {
        $this->validateParameters();
        return $this->createAuthenticationRequest();
    }

    private function createAuthenticationRequest()
    {
        $request = new AuthenticationRequest();
        $request->setRelyingPartyUUID($this->getRelyingPartyUUID());
        $request->setRelyingPartyName($this->getRelyingPartyName());
        $request->setPhoneNumber($this->getPhoneNumber());
        $request->setNationalIdentityNumber($this->getNationalIdentityNumber());
        $request->setHash($this->getHashInBase64());
        $request->setHashType($this->getHashType());
        $request->setLanguage($this->getLanguage());
        $request->setDisplayText($this->getDisplayText());
        $request->setDisplayTextFormat($this->getDisplayTextFormat());
        return $request;
    }

    protected function validateParameters()
    {
        parent::validateParameters();
        parent::validateExtraParameters();
    }


}
