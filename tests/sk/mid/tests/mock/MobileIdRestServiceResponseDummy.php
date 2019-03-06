<?php
/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/20/2019
 * Time: 5:34 PM
 */
namespace sk\mid\tests\mock;

use sk\mid\rest\dao\SessionStatus;

class MobileIdRestServiceResponseDummy
{
    public static function assertAuthenticationPolled(SessionStatus $sessionStatus)
    {
        self::assertSessionStatusPolled($sessionStatus);
        assert(!is_null($sessionStatus->getCert()) && !empty($sessionStatus->getCert()));
    }

    private static function assertSessionStatusPolled(SessionStatus $sessionStatus)
    {
        assert(!is_null($sessionStatus));
        assert(!is_null($sessionStatus->getState()) && !empty($sessionStatus->getState()));
        assert(!is_null($sessionStatus->getResult()) && !empty($sessionStatus->getResult()));
    }


}