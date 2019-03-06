<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/sk/mid/MobileIdAuthenticationHashToSign.php';
require_once __DIR__ . '/../../../src/sk/mid/VerificationCodeCalculator.php';
require_once __DIR__ . '/../../../src/sk/mid/exception/MissingOrInvalidParameterException.php';

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 5:03 PM
 */
class MobileIdAuthenticationHashToSignTest extends TestCase
{


    /**
     * @test
     * @throws Exception
     */
    public function setHashInBase64_calculateVerificationCode_withSHA384()
    {
        $MobileIdAuthenticationHashToSign = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA384)
            ->build();

        // TODO asser

        $this->assertEquals(4, strlen($MobileIdAuthenticationHashToSign->calculateVerificationCode()));
    }

    /**
     * @test
     * @throws Exception
     */
    public function setHashInBase64_calculateVerificationCode_withSHA512()
    {
        $MobileIdAuthenticationHashToSign = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA512)
            ->build();
        $this->assertEquals(4, strlen($MobileIdAuthenticationHashToSign->calculateVerificationCode()));
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function checkFields_withoutHashType()
    {
        MobileIdAuthenticationHashToSign::newBuilder()
            ->build();
    }


}