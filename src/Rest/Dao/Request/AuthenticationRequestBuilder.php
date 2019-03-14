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
namespace Sk\Mid\Rest\Dao\Request;
use Sk\Mid\MobileIdAuthenticationHashToSign;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\Language\Language;
use Sk\Mid\HashType\HashType;
use Sk\Mid\Util\MidInputUtil;

class AuthenticationRequestBuilder
{
    /** @var string $relyingPartyName */
    private $relyingPartyName;

    /** @var string $relyingPartyUUID */
    private $relyingPartyUUID;

    /** @var string $phoneNumber */
    private $phoneNumber;

    /** @var string $nationalIdentityNumber */
    private $nationalIdentityNumber;

    /** @var MobileIdAuthenticationHashToSign $hashToSign */
    private $hashToSign;

    /** @var Language $language */
    private $language;

    /** @var string $displayText */
    private $displayText;

    /** @var string $displayTextFormat */
    private $displayTextFormat;


    public function withRelyingPartyUUID(?string $relyingPartyUUID) : AuthenticationRequestBuilder
    {
        $this->relyingPartyUUID = $relyingPartyUUID;
        return $this;
    }

    public function withRelyingPartyName(?string $relyingPartyName) : AuthenticationRequestBuilder
    {
        $this->relyingPartyName = $relyingPartyName;
        return $this;
    }

    public function withPhoneNumber(string $phoneNumber) : AuthenticationRequestBuilder
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function withNationalIdentityNumber(string $nationalIdentityNumber) : AuthenticationRequestBuilder
    {
        $this->nationalIdentityNumber = $nationalIdentityNumber;
        return $this;
    }

    public function withHashToSign(MobileIdAuthenticationHashToSign $hashToSign) : AuthenticationRequestBuilder
    {
        $this->hashToSign = $hashToSign;
        return $this;
    }

    public function withLanguage(Language $language) : AuthenticationRequestBuilder
    {
        $this->language = $language;
        return $this;
    }

    public function withDisplayText(string $displayText) : AuthenticationRequestBuilder
    {
        $this->displayText = $displayText;
        return $this;
    }

    public function withDisplayTextFormat(string $displayTextFormat) : AuthenticationRequestBuilder
    {
        $this->displayTextFormat = $displayTextFormat;
        return $this;
    }

    public function build() : AuthenticationRequest
    {
        $this->validateParameters();

        $request = new AuthenticationRequest();
        $request->setRelyingPartyUUID($this->getRelyingPartyUUID());
        $request->setRelyingPartyName($this->getRelyingPartyName());
        $request->setPhoneNumber($this->getPhoneNumber());
        $request->setNationalIdentityNumber($this->getNationalIdentityNumber());
        $request->setHash($this->getHashToSign()->getHashInBase64());
        $request->setHashType($this->getHashToSign()->getHashType()->getHashTypeName());
        $request->setLanguage($this->getLanguage());
        $request->setDisplayText($this->getDisplayText());
        $request->setDisplayTextFormat($this->getDisplayTextFormat());
        return $request;

    }


    private function validateParameters()
    {
        MidInputUtil::validateUserInput($this->phoneNumber, $this->nationalIdentityNumber);

        if (is_null($this->hashToSign)) {
            throw new MissingOrInvalidParameterException("hashToSign must be set");
        }

        if (is_null($this->language)) {
            throw new MissingOrInvalidParameterException("Language for user dialog in mobile phone must be set");
        }
    }

    private function getHashToSign() : MobileIdAuthenticationHashToSign {
        return $this->hashToSign;
    }

    private function getRelyingPartyName() : ?string
    {
        return $this->relyingPartyName;
    }

    private function getRelyingPartyUUID() : ?string
    {
        return $this->relyingPartyUUID;
    }

    private function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    private function getNationalIdentityNumber(): string
    {
        return $this->nationalIdentityNumber;
    }

    protected function getHashType() : HashType
    {
        return $this->getHashToSign()->getHashType();
    }

    protected function getHashInBase64() : string
    {
        return $this->getHashToSign()->getHashInBase64();
    }

    private function getLanguage() : Language
    {
        return $this->language;
    }

    private function getDisplayText() : ?string
    {
        return $this->displayText;
    }

    private function getDisplayTextFormat() : ?string
    {
        return $this->displayTextFormat;
    }

}
