<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2021 SK ID Solutions AS
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
namespace Sk\Mid\Tests\Mock;

use Sk\Mid\Rest\Dao\SessionStatus;

class SessionStatusDummy
{
    public static function createRunningSessionStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setState("RUNNING");
        return $sessionStatus;
    }

    public static function createSuccessfulSessionStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("OK");
        return $sessionStatus;
    }

    public static function createTimeoutSessionStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("TIMEOUT");
        return $sessionStatus;
    }

    public static function createResponseRetrievingErrorStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("ERROR");
        return $sessionStatus;
    }

    public static function createNotMIDClientStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("NOT_MID_CLIENT");
        return $sessionStatus;
    }

    public static function createMSSPTransactionExpiredStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("EXPIRED_TRANSACTION");
        return $sessionStatus;
    }

    public static function createUserCancellationStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("USER_CANCELLED");
        return $sessionStatus;
    }

    public static function createMIDNotReadyStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("MID_NOT_READY");
        return $sessionStatus;
    }

    public static function createSimNotAvailableStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("PHONE_ABSENT");
        return $sessionStatus;
    }

    public static function createDeliveryErrorStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("DELIVERY_ERROR");
        return $sessionStatus;
    }

    public static function createInvalidCardResponseStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("SIM_ERROR");
        return $sessionStatus;
    }

    public static function createSignatureHashMismatchStatus() : SessionStatus
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("SIGNATURE_HASH_MISMATCH");
        return $sessionStatus;
    }

    public static function createCompleteSessionStatus() : SessionStatus
    {
        $sessionStatus = new SessionStatus();
        $sessionStatus->setState("COMPLETE");
        return $sessionStatus;
    }

    public static function assertCompleteSessionStatus(SessionStatus $sessionStatus)
    {
        assert(!is_null($sessionStatus));
        assert($sessionStatus->getState() == "COMPLETE");
    }

    public static function assertSuccessfulSessionStatus(SessionStatus $sessionStatus)
    {
        assert($sessionStatus->getState() == "COMPLETE");
        assert($sessionStatus->getResult() == "OK");
    }

    public static function assertErrorSessionStatus(SessionStatus $sessionStatus, string $result)
    {
        assert($sessionStatus->getState() == "COMPLETE");
        assert($sessionStatus->getResult() == $result);
    }







}
