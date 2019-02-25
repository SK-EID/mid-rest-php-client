<?php
/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 1:12 PM
 */
class SessionStatusPollerDummy
{
    public static function pollSessionStatus($connector, $sessionId, $path)
    {
        $sessionStatus = null;
        while ($sessionStatus == null || strcasecmp("RUNNING", $sessionStatus->getState()))
        {
            $request = new SessionStatusRequest($sessionId);
            $sessionStatus = $connector->getSessionStatus($request, $path);
            sleep(1);
        }
        assert($sessionStatus->getState() == "COMPLETE");
        return $sessionStatus;
    }
}