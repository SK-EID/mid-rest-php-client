<?php
require_once __DIR__ . '/../../ee.sk.mid/rest/MobileIdConnector.php';
/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/20/2019
 * Time: 4:33 PM
 */
class MobileIdConnectorStub implements MobileIdConnector
{
    private $sessionIdUsed;
    private $requestUsed;
    private $responses = array();
    private $responseNumber = 0;

    /**
     * @return mixed
     */
    public function getSessionIdUsed()
    {
        return $this->sessionIdUsed;
    }

    /**
     * @return mixed
     */
    public function getRequestUsed()
    {
        return $this->requestUsed;
    }

    /**
     * @return array
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @return int
     */
    public function getResponseNumber()
    {
        return $this->responseNumber;
    }


    public function getCertificate($request)
    {
        return null;
    }

    public function sign($request)
    {
        return null;
    }

    public function authenticate($request)
    {
        return null;
    }

    public function getSessionStatus($request, $path)
    {
        $this->sessionIdUsed = $request->getSessionId();
        $requestUsed = $request;
        return $this->responses[$this->responseNumber + 1];
    }

    public function getAuthenticationSessionStatus($request)
    {
        return $this->getSessionStatus($request, SessionStatusPoller::AUTHENTICATION_SESSION_PATH);
    }

    public function getSignatureSessionStatus($request)
    {
        return $this->getSessionStatus($request, SessionStatusPoller::SIGNATURE_SESSION_PATH);
    }
}