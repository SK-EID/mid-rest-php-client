<?php
/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 1:01 PM
 */
class SessionStatusDummy
{
    public static function createRunningSessionStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setState("RUNNING");
        return $sessionStatus;
    }

    public static function createSuccessfulSessionStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("OK");
        return $sessionStatus;
    }

    public static function createTimeoutSessionStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("TIMEOUT");
        return $sessionStatus;
    }

    public static function createResponseRetrievingErrorStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("ERROR");
        return $sessionStatus;
    }

    public static function createNotMIDClientStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("NOT_MID_CLIENT");
        return $sessionStatus;
    }

    public static function createMSSPTransactionExpiredStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("EXPIRED_TRANSACTION");
        return $sessionStatus;
    }

    public static function createUserCancellationStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("USER_CANCELLED");
        return $sessionStatus;
    }

    public static function createMIDNotReadyStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("MID_NOT_READY");
        return $sessionStatus;
    }

    public static function createSimNotAvailableStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("PHONE_ABSENT");
        return $sessionStatus;
    }

    public static function createDeliveryErrorStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("DELIVERY_ERROR");
        return $sessionStatus;
    }

    public static function createInvalidCardResponseStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("SIM_ERROR");
        return $sessionStatus;
    }

    public static function createSignatureHashMismatchStatus()
    {
        $sessionStatus = self::createCompleteSessionStatus();
        $sessionStatus->setResult("SIGNATURE_HASH_MISMATCH");
        return $sessionStatus;
    }

    public static function createCompleteSessionStatus()
    {
        $sessionStatus = new SessionStatus();
        $sessionStatus->setResult("COMPLETE");
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
