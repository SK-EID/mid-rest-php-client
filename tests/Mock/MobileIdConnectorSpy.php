<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2021 SK ID Solutions AS
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
namespace Sk\Mid\Tests\Mock;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Rest\Dao\Request\SessionStatusRequest;
use Sk\Mid\Rest\Dao\Response\AuthenticationResponse;
use Sk\Mid\Rest\Dao\Response\CertificateResponse;
use Sk\Mid\Rest\Dao\SessionStatus;
use Sk\Mid\Rest\MobileIdConnector;

class MobileIdConnectorSpy implements MobileIdConnector
{

    /** @var SessionStatus $sessionStatusToRespond */
    private $sessionStatusToRespond;

    /** @var CertificateResponse $certificateChoiceResponseToRespond */
    private $certificateChoiceResponseToRespond;

    /** @var AuthenticationResponse $authenticationResponseToRespond */
    private $authenticationResponseToRespond;

    private $signatureResponseToRespond;

    /** @var String $sessionIdUsed */
    private $sessionIdUsed;

    /** @var CertificateRequest $certificateRequestUsed */
    private $certificateRequestUsed;

    /** @var AuthenticationRequest $authenticationRequestUsed */
    private $authenticationRequestUsed;

    private $signatureRequestUsed;

    public function getSessionStatusToRespond(): SessionStatus
    {
        return $this->sessionStatusToRespond;
    }

    public function setSessionStatusToRespond(SessionStatus $sessionStatusToRespond): void
    {
        $this->sessionStatusToRespond = $sessionStatusToRespond;
    }

    public function getCertificateChoiceResponseToRespond(): CertificateResponse
    {
        return $this->certificateChoiceResponseToRespond;
    }

    public function setCertificateChoiceResponseToRespond(CertificateResponse $certificateChoiceResponseToRespond): void
    {
        $this->certificateChoiceResponseToRespond = $certificateChoiceResponseToRespond;
    }

    public function setAuthenticationResponseToRespond(AuthenticationResponse $authenticationResponseToRespond): void
    {
        $this->authenticationResponseToRespond = $authenticationResponseToRespond;
    }

    public function setSignatureResponseToRespond($signatureResponseToRespond)
    {
        $this->signatureResponseToRespond = $signatureResponseToRespond;
    }

    public function getSessionIdUsed(): String
    {
        return $this->sessionIdUsed;
    }

    public function getCertificateRequestUsed(): CertificateRequest
    {
        return $this->certificateRequestUsed;
    }

    public function getAuthenticationRequestUsed(): AuthenticationRequest
    {
        return $this->authenticationRequestUsed;
    }

    public function getSignatureRequestUsed()
    {
        return $this->signatureRequestUsed;
    }

    public function pullCertificate(CertificateRequest $request) : CertificateResponse
    {
        $this->certificateRequestUsed = $request;
        return $this->certificateChoiceResponseToRespond;
    }

    public function sign($request)
    {
        $this->signatureRequestUsed = $request;
        return $this->signatureResponseToRespond;
    }

    public function initAuthentication(AuthenticationRequest $request) : AuthenticationResponse
    {
        $this->authenticationRequestUsed = $request;
        return $this->authenticationResponseToRespond;
    }

    public function getSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        $this->sessionIdUsed = $request->getSessionId();
        return $this->sessionStatusToRespond;
    }

    public function pullAuthenticationSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        return $this->getSessionStatus($request);
    }

}
