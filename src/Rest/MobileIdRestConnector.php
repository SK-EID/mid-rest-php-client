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
namespace Sk\Mid\Rest;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Exception\MidSessionNotFoundException;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\Exception\NotMidClientException;
use Sk\Mid\Exception\UnauthorizedException;
use Sk\Mid\Rest\Dao\Request\AbstractRequest;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Rest\Dao\Request\SessionStatusRequest;
use Sk\Mid\Rest\Dao\Response\AuthenticationResponse;
use Sk\Mid\Rest\Dao\Response\CertificateResponse;
use Sk\Mid\Rest\Dao\SessionStatus;
use Sk\Mid\Util\Logger;

class MobileIdRestConnector implements MobileIdConnector
{

    /** @var Logger $logger */
    private $logger;

    const CERTIFICATE_PATH = '/certificate';
    const SIGNATURE_PATH = '/signature';
    const AUTHENTICATION_PATH = '/authentication';

    const RESPONSE_ERROR_CODES = array(
        503 => 'Limit exceeded',
        403 => 'Forbidden!',
        401 => 'Unauthorized',

        580 => 'System is under maintenance, retry later',
        480 => 'The client is old and not supported any more. Relying Party must contact customer support.',
        472 => 'Person should view app or self-service portal now.',
        471 => 'No suitable account of requested type found, but user has some other accounts.',
    );

    /** @var string $endpointUrl */
    private $endpointUrl;

    /** @var string $clientConfig */
    private $clientConfig;

    /** @var string $relyingPartyUUID */
    private $relyingPartyUUID;

    /** @var string $relyingPartyName */
    private $relyingPartyName;

    public function __construct(MobileIdRestConnectorBuilder $builder)
    {
        $this->logger = new Logger('MobileIdRestConnector');
        $this->endpointUrl = $builder->getEndpointUrl();
        $this->clientConfig = $builder->getClientConfig();
        $this->relyingPartyName = $builder->getRelyingPartyName();
        $this->relyingPartyUUID = $builder->getRelyingPartyUUID();
    }

    public function pullCertificate(CertificateRequest $request) : CertificateResponse
    {
        $this->setRequestRelyingPartyDetailsIfMissing($request);

        $this->logger->debug('Getting certificate for phone number: ' . $request->toString());
        $uri = $this->endpointUrl . '/certificate';
        $this->logger->debug('From uri: ' . $uri);

        $certificateResponse = $this->postCertificateRequest($uri, $request);
        return $certificateResponse;
    }

    public function initAuthentication(AuthenticationRequest $request) : AuthenticationResponse
    {
        $this->setRequestRelyingPartyDetailsIfMissing($request);
        $url = $this->endpointUrl . '/authentication';
        return $this->postAuthenticationRequest($url, $request);
    }

    private function setRequestRelyingPartyDetailsIfMissing(AbstractRequest $request) : void
    {
        if (is_null($request->getRelyingPartyUUID())) {
            $request->setRelyingPartyUUID($this->relyingPartyUUID);
        }
        if (is_null($request->getRelyingPartyName())) {
            $request->setRelyingPartyName($this->relyingPartyName);
        }
        if (empty($request->getRelyingPartyUUID())) {
            throw new MissingOrInvalidParameterException('Relying Party UUID parameter must be set in client or request');
        }
        if (empty($request->getRelyingPartyName())) {
            throw new MissingOrInvalidParameterException('Relying Party Name parameter must be set in client or request');
        }
    }

    public function pullAuthenticationSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        $url = $this->endpointUrl. '/authentication/session/' . $request->getSessionId();

        if ($request->getSessionStatusResponseSocketTimeoutMs() != null) {
            $url = $url . '?timeoutMs='.$request->getSessionStatusResponseSocketTimeoutMs();
        }

        $this->logger->debug('Sending get request to ' . $url);
        $responseAsArray = $this->getRequest($url);
        if (isset($responseAsArray['error'])) throw new MidSessionNotFoundException($request->getSessionId());
        return new SessionStatus($responseAsArray);
    }


    private function postCertificateRequest(string $uri, CertificateRequest $request) : CertificateResponse
    {
        $responseJson = $this->postRequest($uri, $request);
        if (isset($responseJson['error'])) {
            throw new UnauthorizedException();
        } else {
            $this->validateCertificateResult($responseJson['result']);
        }
        return new CertificateResponse($this->postRequest($uri, $request));
    }

    private function validateCertificateResult(string $result)
    {
        $result = strtoupper($result);

        switch ($result) {
            case 'OK':
                return;
            case 'NOT_FOUND':
            case 'NOT_ACTIVE':
                $this->logger->error("No certificate for the user were found");
                throw new NotMidClientException();
            default:
                $this->logger->error("MID returned error code '" . $result . "'");
                throw new MidInternalErrorException("MID returned error code '" . $result . "'");
        }
        
    }

    private function postAuthenticationRequest(string $uri, AuthenticationRequest $request) : AuthenticationResponse
    {
        $responseJson = $this->postRequest($uri, $request);
        return new AuthenticationResponse($responseJson);
    }

    private function postRequest(string $url, AbstractRequest $paramsForJson) : array
    {
        $json = json_encode($paramsForJson);
        $this->logger->debug('POST '.$url.' contents: ' . $json);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json))
        );

        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $this->logger->debug('Response was '.$result.', status code was '.$httpcode);
        $responseAsArray = json_decode($result, true);

        switch ($httpcode) {
            case 200:
                return $responseAsArray;
            case 400:
            case 405:
                throw new MissingOrInvalidParameterException($responseAsArray['error']);
            case 401:
                throw new UnauthorizedException($responseAsArray['error']);
            default:
                throw new MidInternalErrorException('POST request to MID returned unknown status code '.$httpcode);
        }

    }

    public static function newBuilder() : MobileIdRestConnectorBuilder
    {
        return new MobileIdRestConnectorBuilder();
    }


    private function getRequest(string $url) : array
    {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array('Content-Type: application/json')
        );
        $result = curl_exec($ch);

        $this->logger->debug('Result is '. $result);

        return json_decode($result, true);
    }


}
