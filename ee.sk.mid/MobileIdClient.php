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
require_once __DIR__ . '/util/Logger.php';
require_once __DIR__ . '/rest/SessionStatusPoller.php';
require_once __DIR__ . '/exception/CertificateNotPresentException.php';
require_once __DIR__ . '/exception/CertificateRevokedException.php';
require_once __DIR__ . '/exception/TechnicalErrorException.php';
require_once __DIR__ . '/rest/MobileIdRestConnector.php';
require_once 'MobileIdSignature.php';
require_once 'MobileIdAuthentication.php';
require_once 'CertificateParser.php';
require_once 'MobileIdClientBuilder.php';
class MobileIdClient
{

    public static $logger;
    private $relyingPartyUUID;
    private $relyingPartyName;
    private $hostUrl;
    private $networkConnectionConfig;
    private $pollingSleepTimeoutSeconds;
    private $connector;
    private $sessionStatusPoller;

    public function __construct($builder)
    {
        self::$logger = new Logger('MobileIdClient');
        $this->relyingPartyUUID = $builder->getRelyingPartyUUID();
        $this->relyingPartyName = $builder->getRelyingPartyName();
        $this->hostUrl = $builder->getHostUrl();
        $this->networkConnectionConfig = $builder->getNetworkConnectionConfig();
        $this->pollingSleepTimeoutSeconds = $builder->getPollingSleepTimeoutSeconds();
        $this->connector = $builder->getConnector();
        $this->createSessionStatusPoller();
    }

    public function getMobileIdConnector()
    {
        if (is_null($this->connector))
        {
            $this->connector = MobileIdRestConnector::newBuilder()
                ->withEndpointUrl($this->hostUrl)
                ->withClientConfig($this->networkConnectionConfig)
                ->withRelyingPartyUUID($this->relyingPartyUUID)
                ->withRelyingPartyName($this->relyingPartyName)
                ->build();
        }
        return $this->connector;
    }

    public function getSessionStatusPoller()
    {
        return $this->sessionStatusPoller;
    }

    public function getRelyingPartyUUID()
    {
        return $this->relyingPartyUUID;
    }

    public function getRelyingPartyName()
    {
        return $this->relyingPartyName;
    }

    private function createSessionStatusPoller()
    {
        $sessionStatusPoller = new SessionStatusPoller($this->getMobileIdConnector());
        $sessionStatusPoller->setPollingSleepTimeSeconds($this->pollingSleepTimeoutSeconds);
        $this->sessionStatusPoller = $sessionStatusPoller;
        return $sessionStatusPoller;
    }

    public function createMobileIdCertificate($certificateChoiceResponse)
    {
        $this->validateCertificateResult($certificateChoiceResponse->getResult());
        $this->validateCertificateResponse($certificateChoiceResponse);
        return CertificateParser::parseX509Certificate($certificateChoiceResponse->getCert());
    }

    public function createMobileIdSignature($sessionStatus)
    {
        $this->validateResponse($sessionStatus);
        $sessionSignature = $sessionStatus->getSignature();
        return MobileIdSignature::newBuilder()
            ->withValueInBase64($sessionSignature->getValue())
            ->withAlgorithmName($sessionSignature->getAlgorithm())
            ->build();
    }

    public function createMobileIdAuthentication($sessionStatus, $hashInBase64, $hashType)
    {
        $this->validateResponse($sessionStatus);
        $sessionSignature = $sessionStatus->getSignature();
        $certificate = CertificateParser::parseX509Certificate($sessionStatus->getCert());
        return MobileIdAuthentication::newBuilder()
            ->withResult($sessionStatus->getResult())
            ->withSignatureValueInBase64($sessionSignature->getValue())
            ->withAlgorithmName($sessionSignature->getAlgorithm())
            ->withCertificate($certificate)
            ->withSignedHashInBase64($hashInBase64)
            ->withHashType($hashType)
            ->build();

    }

    private function validateCertificateResult($result)
    {
        if (strcasecmp('NOT_FOUND', $result) == 0) {
            self::$logger->error('No certificate for the user was found');
            throw new CertificateNotPresentException('No certificate for the user was found');
        } else if (strcasecmp('NOT_ACTIVE', $result) == 0) {
            self::$logger->error('Inactive certificate found');
            throw new CertificateRevokedException('Inactive certificate found');
        } else if (!strcasecmp('OK', $result) == 0) {
            self::$logger->error("Session status end result is '".$result."'");
            throw new TechnicalErrorException("Session status end result is '".$result."'");
        }
    }

    private function validateCertificateResponse($certificateChoiceResponse)
    {
        if (is_null($certificateChoiceResponse->getCert()) || empty($certificateChoiceResponse->getCert())) {
            self::$logger->error('Certificate was not present in the session status response');
            throw new TechnicalErrorException('Certificate was not present in the session status response');
        }
    }


    private function validateResponse($sessionStatus)
    {
        if (is_null($sessionStatus->getSignature()) || empty($sessionStatus->getSignature()->getValue()))
        {
            self::$logger->error('Signature was not present in the response');
            throw new TechnicalErrorException('Signature was not present in the response');
        }
    }

    public static function newBuilder()
    {
        return new MobileIdClientBuilder();
    }
}


