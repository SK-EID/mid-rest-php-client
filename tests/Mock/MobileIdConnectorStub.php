<?php
namespace Sk\Mid\Tests\Mock;
use Sk\Mid\Rest\Dao\Request\SessionStatusRequest;
use Sk\Mid\Rest\Dao\SessionStatus;
use Sk\Mid\Rest\MobileIdConnector;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\Dao\Response\CertificateResponse;
use Sk\Mid\Rest\Dao\Response\AuthenticationResponse;
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


    public function pullCertificate(CertificateRequest $request) : CertificateResponse
    {
        return null;
    }

    public function sign($request)
    {
        return null;
    }

    public function initAuthentication(AuthenticationRequest $request) : AuthenticationResponse
    {
        return null;
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
