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
require_once __DIR__ . '/dao/response/CertificateChoiceResponse.php';
require_once __DIR__ . '/dao/response/AuthenticationResponse.php';
require_once __DIR__ . '/dao/SessionStatus.php';
require_once 'MobileIdConnector.php';
require_once 'MobileIdRestConnectorBuilder.php';

class MobileIdRestConnector implements MobileIdConnector
{

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

    private $endpointUrl;
    private $clientConfig;
    private $relyingPartyUUID;
    private $relyingPartyName;
    private $curl;

    public function __construct($builder)
    {
        $this->logger = new Logger('MobileIdRestConnector');
        $this->endpointUrl = $builder->getEndpointUrl();
        $this->clientConfig = $builder->getClientConfig();
        $this->relyingPartyName = $builder->getRelyingPartyName();
        $this->relyingPartyUUID = $builder->getRelyingPartyUUID();
    }

    public function getCertificate($request)
    {
        $this->setRequestRelyingPartyDetailsIfMissing($request);

        $this->logger->debug('Getting certificate for phone number: ' . $request->getPhoneNumber());
        $uri = $this->endpointUrl . '/certificate';

        $certificateResponse = $this->postCertificateRequest($uri, $request);
        self::validateCertificateResult($certificateResponse->result);

        return $certificateResponse;
    }


    private function validateCertificateResult($result)
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


    public function sign($request)
    {
        $this->setRequestRelyingPartyDetailsIfMissing($request);
        $this->logger->debug('Signing for phone number: ' . $request->getPhoneNumber());
        $uri = $this->endpointUrl . '/signature';
        return $this->postSignatureRequest($uri, $request);
    }

    public function authenticate($request)
    {
        $this->setRequestRelyingPartyDetailsIfMissing($request);
        $url = $this->endpointUrl . '/authentication';
        return $this->postAuthenticationRequest($url, $request);
    }

    private function setRequestRelyingPartyDetailsIfMissing($request)
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

    public function getSessionStatus($request, $path)
    {
        $url = $this->endpointUrl.$path;
        $url = str_replace('{sessionId}', $request->getSessionId(), $url);

        if ($request->getSessionStatusResponseSocketTimeoutMs() != null) {
            $url = $url . '?timeoutMs='.$request->getSessionStatusResponseSocketTimeoutMs();
        }

        $this->logger->debug('Sending get request to ' . $url);
        try {
            $responseAsArray = $this->getRequest($url);
            return new SessionStatus($responseAsArray);
        } catch (Exception $e) {
            throw new SessionNotFoundException();
        }
    }

    public function getAuthenticationSessionStatus($request)
    {
        return $this->getSessionStatus($request, '/authentication/session/');
    }

    public function getSignatureSessionStatus($request)
    {
        return $this->getSessionStatus($request, '/signature/session/');
    }

    private function postCertificateRequest($uri, $request)
    {
        return $this->postRequest($uri, $request, CertificateChoiceResponse::class);
    }

    private function postSignatureRequest($uri, $request)
    {
        return $this->postRequest($uri, $request, SignatureResponse::class);
    }

    private function postAuthenticationRequest($uri, $request)
    {
        return $this->postRequest($uri, $request->toArray(), AuthenticationResponse::class);
    }

    private function postRequest($url, $params, $responseType)
    {
        $json = json_encode($params);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json))
        );

        $result = curl_exec($ch);
        return json_decode($result); // TODO cast to correct response type
    }

    public static function newBuilder()
    {
        return new MobileIdRestConnectorBuilder();
    }

    private function request($url, $responseType)
    {
        $rawResponse = $this->curl->fetch();
        if (false !== ($error = $this->curl->getError())) {
            throw new MobileIdException($error);
        }

        $httpCode = $this->curl->getCurlInfo(CURLINFO_HTTP_CODE);

        $this->curl->closeRequest();

        if (array_key_exists($httpCode, self::RESPONSE_ERROR_CODES)) {
            throw new MobileIdException(self::RESPONSE_ERROR_CODES[$httpCode], $httpCode);
        }

        if (404 == $httpCode) {
            throw new MobileIdException('User account not found for URI ' . $url);
        }
        $response = $this->getResponse($rawResponse, $responseType);
        return $response;
    }

    private function getRequest($url)
    {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json')
        );
        $result = curl_exec($ch);

        $this->logger->debug('Result is '. $result);

        return json_decode($result, true);
    }

    private function getResponse($rawResponse, $responseType)
    {
        $preparedResponse = json_decode($rawResponse, true);
        return new $responseType($preparedResponse);
    }

    private function setNetworkInterface(array &$params)
    {
        if (isset($params['networkInterface'])) {
            $this->curl->setCurlParam(CURLOPT_INTERFACE, $params['networkInterface']);
            unset($params['networkInterface']);
        }
    }
}
