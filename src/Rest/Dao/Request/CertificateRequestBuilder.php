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
use Sk\Mid\Util\Logger;
use Sk\Mid\Exception\MissingOrInvalidParameterException;

class CertificateRequestBuilder
{
    /** @var Logger $logger */
    public static $logger;

    /** @var string $relyingPartyName */
    private $relyingPartyName;

    /** @var string $relyingPartyUUID */
    private $relyingPartyUUID;

    /** @var string $phoneNumber */
    private $phoneNumber;

    /** @var string $nationalIdentityNumber */
    private $nationalIdentityNumber;

    public function __construct()
    {
        self::$logger = new Logger('CertificateRequestBuilder');
    }

    public function withRelyingPartyUUID(?string $relyingPartyUUID) : CertificateRequestBuilder
    {
        $this->relyingPartyUUID = $relyingPartyUUID;
        return $this;
    }

    public function withRelyingPartyName(?string $relyingPartyName) : CertificateRequestBuilder
    {
        $this->relyingPartyName = $relyingPartyName;
        return $this;
    }

    public function withPhoneNumber(string $phoneNumber) : CertificateRequestBuilder
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function withNationalIdentityNumber(string $nationalIdentityNumber) : CertificateRequestBuilder
    {
        $this->nationalIdentityNumber = $nationalIdentityNumber;
        return $this;
    }

    public function build() : CertificateRequest
    {
        $this->validateParameters();
        $request = new CertificateRequest();
        $request->setRelyingPartyUUID($this->getRelyingPartyUUID());
        $request->setRelyingPartyName($this->getRelyingPartyName());
        $request->setPhoneNumber($this->getPhoneNumber());
        $request->setNationalIdentityNumber($this->getNationalIdentityNumber());
        return $request;
    }

    public function getRelyingPartyName(): ?string
    {
        return $this->relyingPartyName;
    }

    public function getRelyingPartyUUID(): ?string
    {
        return $this->relyingPartyUUID;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getNationalIdentityNumber(): string
    {
        return $this->nationalIdentityNumber;
    }

    private function validateParameters()
    {
        if (empty($this->phoneNumber) || empty($this->nationalIdentityNumber)) {
            self::$logger->error('Phone number and national identity must be set');
            throw new MissingOrInvalidParameterException('Phone number and national identity must be set');
        }
    }

}
