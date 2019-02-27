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
require_once __DIR__ . '/dao/request/SessionStatusRequest.php';
require_once __DIR__ . '/../exception/TechnicalErrorException.php';
require_once __DIR__ . '/../exception/SessionTimeoutException.php';
require_once __DIR__ . '/../exception/ResponseRetrievingException.php';
require_once __DIR__ . '/../exception/NotMIDClientException.php';
require_once __DIR__ . '/../exception/CertificateRevokedException.php';
require_once __DIR__ . '/../exception/UserCancellationException.php';
require_once __DIR__ . '/../exception/MIDNotReadyException.php';
require_once __DIR__ . '/../exception/SimNotAvailableException.php';
require_once __DIR__ . '/../exception/DeliveryException.php';
require_once __DIR__ . '/../exception/InvalidCardResponseException.php';
require_once __DIR__ . '/../exception/SignatureHashMismatchException.php';

class SessionStatusPoller
{

    const SIGNATURE_SESSION_PATH = '/signature/session/{sessionId}';
    const AUTHENTICATION_SESSION_PATH = '/authentication/session/{sessionId}';
    private $logger;
    private $connector;
    private $pollingSleepTimeoutSeconds = 1;

    public function __construct($connector)
    {
        $this->logger = new Logger('SessionStatuspoller');
        $this->connector = $connector;
    }

    public function fetchFinalSignatureSessionStatus($sessionId)
    {
        return $this->fetchFinalSessionStatus($sessionId, self::SIGNATURE_SESSION_PATH, null);
    }

    public function fetchFinalAuthenticationSession($sessionId)
    {
        return $this->fetchFinalSessionStatus($sessionId, self::AUTHENTICATION_SESSION_PATH, null);
    }

    public function fetchFinalSessionStatus($sessionId, $path)
    {
        $this->logger->debug('Starting to poll session status for session ' . $sessionId);
        $sessionStatus = $this->pollForFinalSessionStatus($sessionId, $path);
        $this->validateResult($sessionStatus);
        return $sessionStatus;
    }

    private function pollForFinalSessionStatus($sessionId, $path)
    {
        $sessionStatus = null;

        while ($sessionStatus == null || strcasecmp($sessionStatus->getState(), 'RUNNING') == 0) {
            $sessionStatus = $this->pollSessionStatus($sessionId, $path);
            if (strcasecmp("COMPLETE", $sessionStatus->getState()) == 0) {
                return $sessionStatus;
            }
            $this->logger->debug('Sleeping for ' . $this->pollingSleepTimeoutSeconds . ' seconds');
            sleep($this->pollingSleepTimeoutSeconds);
        }

        $this->logger->debug('Got session final session status response');
        return $sessionStatus;
    }

    private function pollSessionStatus($sessionId, $path)
    {
        $this->logger->debug('Polling session status');
        $request = $this->createSessionStatusRequest($sessionId);
        return $this->connector->getSessionStatus($request, $path);
    }

    private function createSessionStatusRequest($sessionId)
    {
        return  new SessionStatusRequest($sessionId);
    }

    private function validateResult($sessionStatus)
    {
        $result = $sessionStatus->getResult();
        if ($result == null) {
            $this->logger->error('Result is missing in the session status response');
            throw new TechnicalErrorException('Result is missing in the session status response');
        } else {
            $this->validateResultOfString($result);
        }
    }

    private function validateResultOfString($result)
    {
        if (strcasecmp('TIMEOUT', $result) == 0) { // compare strings case-insensitively
            $this->logger->error('Session timeout');
            throw new SessionTimeoutException();
        } else if (strcasecmp('ERROR', $result) == 0) {
            $this->logger->error('Error getting response from cert-store/MSSP');
            throw new ResponseRetrievingException();
        } else if (strcasecmp('NOT_MID_CLIENT', $result) == 0) {
            $this->logger->error('Given user has no active certificates and is not M-ID client');
            throw new NotMIDClientException();
        } else if (strcasecmp('EXPIRED_TRANSACTION', $result) == 0) {
            $this->logger->error('MSSP transaction timed out');
            throw new CertificateRevokedException();
        } else if (strcasecmp('USER_CANCELLED', $result) == 0) {
            $this->logger->error('User cancelled the operation');
            throw new UserCancellationException();
        } else if (strcasecmp('MID_NOT_READY', $result) == 0) {
            $this->logger->error('Mobile-ID not ready');
            throw new MIDNotReadyException();
        } else if (strcasecmp('PHONE_ABSENT', $result) == 0) {
            $this->logger->error('Sim not available');
            throw new SimNotAvailableException();
        } else if (strcasecmp('DELIVERY_ERROR', $result) == 0) {
            $this->logger->error('SMS sending error');
            throw new DeliveryException();
        } else if (strcasecmp('SIM_ERROR', $result) == 0) {
            $this->logger->error('Invalid response from card');
            throw new InvalidCardResponseException();
        } else if (strcasecmp('SIGNATURE_HASH_MISMATCH', $result) == 0) {
            $this->logger->error('Hash does not match with certificate type');
            throw new SignatureHashMismatchException();
        } else if (strcasecmp('OK', $result) == 0) {
            $this->logger->error("Session status end result is '" . $result . "'");
            throw new TechnicalErrorException("Session status end result is '" . $result . "'");
        }
    }

    public function setPollingSleepTimeSeconds($pollingSleepTimeSeconds)
    {
        $this->logger->debug('Polling sleep time is ' . $pollingSleepTimeSeconds . ' second(s)');
        $this->pollingSleepTimeoutSeconds = $pollingSleepTimeSeconds;
    }


}
