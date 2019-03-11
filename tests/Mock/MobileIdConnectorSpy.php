<?php
namespace Sk\Mid\Tests\Mock;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Rest\Dao\Request\SessionStatusRequest;
use Sk\Mid\Rest\Dao\Response\AuthenticationResponse;
use Sk\Mid\Rest\Dao\Response\CertificateChoiceResponse;
use Sk\Mid\Rest\Dao\SessionStatus;
use Sk\Mid\Rest\MobileIdConnector;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\SessionStatusPoller;


/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/20/2019
 * Time: 4:15 PM
 */
class MobileIdConnectorSpy implements MobileIdConnector
{

    /** @var SessionStatus $sessionStatusToRespond */
    private $sessionStatusToRespond;

    /** @var CertificateChoiceResponse $certificateChoiceResponseToRespond */
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

    public function getCertificateChoiceResponseToRespond(): CertificateChoiceResponse
    {
        return $this->certificateChoiceResponseToRespond;
    }

    public function setCertificateChoiceResponseToRespond(CertificateChoiceResponse $certificateChoiceResponseToRespond): void
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

    public function getCertificate(CertificateRequest $request) : CertificateChoiceResponse
    {
        $this->certificateRequestUsed = $request;
        return $this->certificateChoiceResponseToRespond;
    }

    public function sign($request)
    {
        $this->signatureRequestUsed = $request;
        return $this->signatureResponseToRespond;
    }

    public function authenticate(AuthenticationRequest $request) : AuthenticationResponse
    {
        $this->authenticationRequestUsed = $request;
        return $this->authenticationResponseToRespond;
    }

    public function getSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        $this->sessionIdUsed = $request->getSessionId();
        return $this->sessionStatusToRespond;
    }

    public function getAuthenticationSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        return $this->getSessionStatus($request);
    }

}