<?php

use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/mock/TestData.php';


require_once __DIR__ . '/../../../src/sk/mid/HashType.php';
require_once __DIR__ . '/../../../src/sk/mid/CertificateParser.php';
require_once __DIR__ . '/../../../src/sk/mid/MobileIdAuthentication.php';
require_once __DIR__ . '/../../../src/sk/mid/exception/MissingOrInvalidParameterException.php';

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 5:40 PM
 */
class MobileIdAuthenticationTest extends TestCase
{
    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function setInvalidValueInBase64_shouldThrowException()
    {
        $authentication = MobileIdAuthentication::newBuilder()
            ->withSignatureValueInBase64("!IsNotValidBase64Character")
            ->build();

        $authentication->getSignatureValue();
    }

    /**
     * @test
     * @throws Exception
     */
    public function createMobileIdAuthentication()
    {
        $sha512 = new Sha512();
        $authentication = MobileIdAuthentication::newBuilder()
            ->withResult("OK")
            ->withSignatureValueInBase64("SEFDS0VSTUFO")
            ->withCertificate(CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_EE))
            ->withSignedHashInBase64("K74MSLkafRuKZ1Ooucvh2xa4Q3nz+R/hFWIShN96SPHNcem+uQ6mFMe9kkJQqp5EaoZnJeaFpl310TmlzRgNyQ==")
            ->withHashType(new Sha512())
            ->withAlgorithmName($sha512->getAlgorithmName())
            ->build();

        $this->assertEquals("OK", $authentication->getResult());
        $this->assertEquals("SEFDS0VSTUFO", $authentication->getSignatureValueInBase64());
        $this->assertEquals("SHA-512",$authentication->getAlgorithmName());
        $this->assertEquals("K74MSLkafRuKZ1Ooucvh2xa4Q3nz+R/hFWIShN96SPHNcem+uQ6mFMe9kkJQqp5EaoZnJeaFpl310TmlzRgNyQ==", $authentication->getSignedHashInBase64());
        $this->assertEquals(new Sha512(), $authentication->getHashType());
    }
}