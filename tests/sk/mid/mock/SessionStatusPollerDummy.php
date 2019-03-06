<?php
require_once __DIR__ . '/../../../../src/sk/mid/rest/dao/request/SessionStatusRequest.php';
/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 1:12 PM
 */
class SessionStatusPollerDummy
{
    public static function pollSessionStatus(MobileIdRestConnector $connector, string $sessionId) : SessionStatus
    {
        $sessionStatus = null;
        while ($sessionStatus == null || strcasecmp("RUNNING", $sessionStatus->getState()) == 0)
        {
            $request = new SessionStatusRequest($sessionId);
            $sessionStatus = $connector->getAuthenticationSessionStatus($request);
            sleep(1);
        }
        assert($sessionStatus->getState() == "COMPLETE");
        return $sessionStatus;
    }
}