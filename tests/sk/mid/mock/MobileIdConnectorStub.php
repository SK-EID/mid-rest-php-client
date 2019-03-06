<?php
require_once __DIR__ . '/../../../../src/sk/mid/rest/MobileIdConnector.php';
/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/20/2019
 * Time: 4:33 PM
 */
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


    public function getCertificate(CertificateRequest $request) : CertificateChoiceResponse
    {
        return null;
    }

    public function sign($request)
    {
        return null;
    }

    public function authenticate(AuthenticationRequest $request) : AuthenticationResponse
    {
        return null;
    }

    public function getSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        $this->sessionIdUsed = $request->getSessionId();
        $requestUsed = $request;
        return $this->responses[$this->responseNumber++];
    }

    public function getAuthenticationSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        return $this->getSessionStatus($request);
    }

    public function getSignatureSessionStatus($request)
    {
        return $this->getSessionStatus($request);
    }
}
