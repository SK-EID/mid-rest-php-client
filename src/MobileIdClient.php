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
namespace Sk\Mid;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Exception\NotMidClientException;
use Sk\Mid\Rest\Dao\MidCertificate;
use Sk\Mid\Rest\Dao\Response\CertificateResponse;
use Sk\Mid\Rest\Dao\SessionStatus;
use Sk\Mid\Rest\MobileIdConnector;
use Sk\Mid\Rest\MobileIdRestConnector;
use Sk\Mid\Rest\SessionStatusPoller;
use Sk\Mid\Util\Logger;

class MobileIdClient
{

    /** @var Logger $logger */
    public static $logger;

    /** @var string $relyingPartyUUID */
    private $relyingPartyUUID;

    /** @var string $relyingPartyName */
    private $relyingPartyName;

    /** @var string $hostUrl */
    private $hostUrl;

    /** @var string $networkConnectionConfig */
    private $networkConnectionConfig;

    /** @var MobileIdRestConnector $connector */
    private $connector;

    /** @var SessionStatusPoller $sessionStatusPoller */
    private $sessionStatusPoller;

    public function __construct(MobileIdClientBuilder $builder)
    {
        self::$logger = new Logger('MobileIdClient');
        $this->relyingPartyUUID = $builder->getRelyingPartyUUID();
        $this->relyingPartyName = $builder->getRelyingPartyName();
        $this->hostUrl = $builder->getHostUrl();
        $this->networkConnectionConfig = $builder->getNetworkConnectionConfig();
        $this->connector = $builder->getConnector();
        $this->sessionStatusPoller = SessionStatusPoller::newBuilder()
                ->withConnector($this->getMobileIdConnector())
                ->withPollingSleepTimeoutSeconds($builder->getPollingSleepTimeoutSeconds())
                ->withLongPollingTimeoutSeconds($builder->getLongPollingTimeoutSeconds())
                ->build();
    }

    public function getMobileIdConnector(): MobileIdConnector
    {
        if (is_null($this->connector)) {
            $this->connector = MobileIdRestConnector::newBuilder()
                ->withEndpointUrl($this->hostUrl)
                ->withClientConfig($this->networkConnectionConfig)
                ->withRelyingPartyUUID($this->relyingPartyUUID)
                ->withRelyingPartyName($this->relyingPartyName)
                ->build();
        }
        return $this->connector;
    }

    public function getSessionStatusPoller(): SessionStatusPoller
    {
        return $this->sessionStatusPoller;
    }

    public function getRelyingPartyUUID(): string
    {
        return $this->relyingPartyUUID;
    }

    public function getRelyingPartyName(): string
    {
        return $this->relyingPartyName;
    }

    public function createMobileIdCertificate(CertificateResponse $certificateChoiceResponse): array
    {
        $this->validateCertificateResult($certificateChoiceResponse->getResult());
        $this->validateCertificateResponse($certificateChoiceResponse);
        return CertificateParser::parseX509Certificate($certificateChoiceResponse->getCert());
    }

    public function parseMobileIdIdentity(CertificateResponse $certificateChoiceResponse): MidIdentity
    {
        $midCertificateArray = $this->createMobileIdCertificate($certificateChoiceResponse);

        $midCertificate = new MidCertificate($midCertificateArray);
        return MidIdentity::parseFromCertificate($midCertificate);
    }


    public function createMobileIdAuthentication(SessionStatus $sessionStatus, MobileIdAuthenticationHashToSign $hash): MobileIdAuthentication
    {
        $this->validateResponse($sessionStatus);
        $sessionSignature = $sessionStatus->getSignature();
        $certificate = CertificateParser::parseX509Certificate($sessionStatus->getCert());

        return MobileIdAuthentication::newBuilder()
            ->withResult($sessionStatus->getResult())
            ->withSignatureValueInBase64($sessionSignature->getValue())
            ->withAlgorithmName($sessionSignature->getAlgorithmName())
            ->withCertificate($certificate)
            ->withSignedHashInBase64($hash->getHashInBase64())
            ->withHashType($hash->getHashType())
            ->build();

    }

    private function validateCertificateResult(?string $result): void
    {
        if (strcasecmp('NOT_FOUND', $result) == 0) {
            self::$logger->error('No certificate for the user was found');
            throw new NotMidClientException();
        } else if (strcasecmp('NOT_ACTIVE', $result) == 0) {
            self::$logger->error('Inactive certificate found');
            throw new NotMidClientException();
        } else if (!strcasecmp('OK', $result) == 0) {
            self::$logger->error("Session status end result is '" . $result . "'");
            throw new MidInternalErrorException("Session status end result is '" . $result . "'");
        }
    }

    private function validateCertificateResponse(CertificateResponse $certificateChoiceResponse): void
    {
        if (is_null($certificateChoiceResponse->getCert()) || empty($certificateChoiceResponse->getCert())) {
            self::$logger->error('Certificate was not present in the session status response');
            throw new MidInternalErrorException('Certificate was not present in the session status response');
        }
    }


    private function validateResponse(SessionStatus $sessionStatus): void
    {
        if (is_null($sessionStatus->getSignature()) || empty($sessionStatus->getSignature()->getValue())) {
            self::$logger->error('Signature was not present in the response');
            throw new MidInternalErrorException('Signature was not present in the response');
        }
    }

    public static function newBuilder(): MobileIdClientBuilder
    {
        return new MobileIdClientBuilder();
    }
}


