<?php
/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 1:01 PM
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
