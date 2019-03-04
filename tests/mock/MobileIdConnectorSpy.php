<?php

require_once __DIR__ . '/../../ee.sk.mid/rest/MobileIdConnector.php';
require_once __DIR__ . '/../../ee.sk.mid/rest/dao/request/AuthenticationRequest.php';


/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/20/2019
 * Time: 4:15 PM
 */
class MobileIdConnectorSpy implements MobileIdConnector
{

    private $sessionStatusToRespond;
    private $certificateChoiceResponseToRespond;
    private $authenticationResponseToRespond;
    private $signatureResponseToRespond;
    private $sessionIdUsed;
    private $certificateRequestUsed;
    private $authenticationRequestUsed;
    private $signatureRequestUsed;

    /**
     * @return mixed
     */
    public function getSessionStatusToRespond()
    {
        return $this->sessionStatusToRespond;
    }

    /**
     * @param mixed $sessionStatusToRespond
     */
    public function setSessionStatusToRespond($sessionStatusToRespond)
    {
        $this->sessionStatusToRespond = $sessionStatusToRespond;
    }

    /**
     * @return mixed
     */
    public function getCertificateChoiceResponseToRespond()
    {
        return $this->certificateChoiceResponseToRespond;
    }

    /**
     * @param mixed $certificateChoiceResponseToRespond
     */
    public function setCertificateChoiceResponseToRespond($certificateChoiceResponseToRespond)
    {
        $this->certificateChoiceResponseToRespond = $certificateChoiceResponseToRespond;
    }

    /**
     * @param mixed $authenticationResponseToRespond
     */
    public function setAuthenticationResponseToRespond($authenticationResponseToRespond)
    {
        $this->authenticationResponseToRespond = $authenticationResponseToRespond;
    }

    /**
     * @param mixed $signatureResponseToRespond
     */
    public function setSignatureResponseToRespond($signatureResponseToRespond)
    {
        $this->signatureResponseToRespond = $signatureResponseToRespond;
    }

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
    public function getCertificateRequestUsed()
    {
        return $this->certificateRequestUsed;
    }

    /**
     * @return mixed
     */
    public function getAuthenticationRequestUsed()
    {
        return $this->authenticationRequestUsed;
    }

    /**
     * @return mixed
     */
    public function getSignatureRequestUsed()
    {
        return $this->signatureRequestUsed;
    }

    public function getCertificate($request)
    {
        $this->certificateRequestUsed = $request;
        return $this->certificateChoiceResponseToRespond;
    }

    public function sign($request)
    {
        $this->signatureRequestUsed = $request;
        return $this->signatureResponseToRespond;
    }

    public function authenticate(AuthenticationRequest $request)
    {
        $this->authenticationRequestUsed = $request;
        return $this->authenticationResponseToRespond;
    }

    public function getSessionStatus($request, $path)
    {
        $this->sessionIdUsed = $request->getSessionId();
        return $this->sessionStatusToRespond;
    }

    public function getAuthenticationSessionStatus($request)
    {
        return $this->getSessionStatus($request, SessionStatusPoller::AUTHENTICATION_SESSION_PATH);
    }

}