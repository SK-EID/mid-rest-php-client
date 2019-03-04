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
require_once __DIR__ . '/../util/Logger.php';
require_once __DIR__ . '/../util/Curl.php';
require_once __DIR__ . '/../exception/SessionNotFoundException.php';
require_once __DIR__ . '/../exception/MobileIdException.php';
require_once __DIR__ . '/../exception/NotMIDClientException.php';
require_once __DIR__ . '/dao/request/AuthenticationRequest.php';
require_once __DIR__ . '/dao/response/CertificateChoiceResponse.php';
require_once __DIR__ . '/dao/response/AuthenticationResponse.php';
require_once __DIR__ . '/dao/SessionStatus.php';
require_once 'MobileIdConnector.php';
require_once 'MobileIdRestConnectorBuilder.php';

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

    public function getCertificate(CertificateRequest $request) : CertificateChoiceResponse
    {
        $this->setRequestRelyingPartyDetailsIfMissing($request);

        $this->logger->debug('Getting certificate for phone number: ' . $request->toString());
        $uri = $this->endpointUrl . '/certificate';
        $this->logger->debug('From uri: ' . $uri);

        $certificateResponse = $this->postCertificateRequest($uri, $request);
        return $certificateResponse;
    }

    public function authenticate(AuthenticationRequest $request) : AuthenticationResponse
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
            throw new ParameterMissingException('Relying Party UUID parameter must be set in client or request');
        }
        if (empty($request->getRelyingPartyName())) {
            throw new ParameterMissingException('Relying Party Name parameter must be set in client or request');
        }
    }

    public function getAuthenticationSessionStatus(SessionStatusRequest $request) : SessionStatus
    {
        $url = $this->endpointUrl. '/authentication/session/' . $request->getSessionId();

        if ($request->getSessionStatusResponseSocketTimeoutMs() != null) {
            $url = $url . '?timeoutMs='.$request->getSessionStatusResponseSocketTimeoutMs();
        }

        $this->logger->debug('Sending get request to ' . $url);
        $responseAsArray = $this->getRequest($url);
        if (isset($responseAsArray['error'])) throw new SessionNotFoundException();
        return new SessionStatus($responseAsArray);
    }


    private function postCertificateRequest(string $uri, CertificateRequest $request) : CertificateChoiceResponse
    {
        $responseJson = $this->postRequest($uri, $request);
        if (isset($responseJson['error'])) {
            throw new UnAuthorizedException();
        } else {
            $this->validateCertificateResult($responseJson['result']);
        }
        return new CertificateChoiceResponse($this->postRequest($uri, $request));
    }

    private function validateCertificateResult(string $result)
    {
        if (strcasecmp("NOT_FOUND", $result) == 0) {
            $this->logger->error("No certificate for the user was found");
            throw new NotMIDClientException();
        } else if (strcasecmp("NOT_ACTIVE", $result) == 0) {
            $this->logger->error("Certificate was found but is not active");
            throw new CertificateRevokedException("Inactive certificate found");
        } else if (strcasecmp("OK", $result) != 0) {
            $this->logger->error("Session status end result is '" . $result . "'");
            throw new TechnicalErrorException("Session status end result is '" . $result . "'");
        }
    }

    private function postAuthenticationRequest(string $uri, AuthenticationRequest $request) : AuthenticationResponse
    {
        $responseJson = $this->postRequest($uri, $request);
        if (!isset($responseJson['sessionId'])) throw new UnAuthorizedException();
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

        $this->logger->debug('Response was '.$result);


        return json_decode($result, true);
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
