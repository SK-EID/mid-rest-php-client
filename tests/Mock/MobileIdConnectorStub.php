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
use Sk\Mid\Rest\Dao\Request\SessionStatusRequest;
use Sk\Mid\Rest\Dao\SessionStatus;
use Sk\Mid\Rest\MobileIdConnector;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\Dao\Response\CertificateResponse;
use Sk\Mid\Rest\Dao\Response\AuthenticationResponse;

class MobileIdConnectorStub implements MobileIdConnector
{
    /** @var string $sessionIdUsed */
    private $sessionIdUsed;
    private $requestUsed;
    private $responses = array();

    /** @var int $responseNumber */
    private $responseNumber = 0;

    public function getSessionIdUsed() : string
    {
        return $this->sessionIdUsed;
    }

    public function getRequestUsed()
    {
        return $this->requestUsed;
    }

    public function getResponses() : array
    {
        return $this->responses;
    }

    public function addResponse(SessionStatus $response) : void
    {
        array_push($this->responses, $response);
    }

    public function getResponseNumber() : int
    {
        return $this->responseNumber;
    }


    public function pullCertificate(CertificateRequest $request) : CertificateResponse
    {
        return new CertificateResponse();
    }

    public function sign($request)
    {
        return null;
    }

    public function initAuthentication(AuthenticationRequest $request) : AuthenticationResponse
    {
        return new AuthenticationResponse();
    }

    public function getSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        $this->sessionIdUsed = $request->getSessionId();
        return $this->responses[$this->responseNumber++];
    }

    public function pullAuthenticationSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        return $this->getSessionStatus($request);
    }

    public function getSignatureSessionStatus($request)
    {
        return $this->getSessionStatus($request);
    }
}
