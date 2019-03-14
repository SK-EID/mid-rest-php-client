<?php
namespace Sk\Mid\Tests;
use PHPUnit\Framework\TestCase;

use Sk\Mid\HashType\Sha512;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\HashType;
use Sk\Mid\CertificateParser;
use Sk\Mid\MobileIdAuthentication;
use Sk\Mid\Exception\MissingOrInvalidParameterException;

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
     */
    public function setInvalidValueInBase64_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $authentication = MobileIdAuthentication::newBuilder()
            ->withSignatureValueInBase64("!IsNotValidBase64Character")
            ->build();

        $authentication->getSignatureValue();
    }

    /**
     * @test
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

    /**
     * @test
     * @throws \Exception
     */
    public function constructAuthenticationIdentity_withEECertificate()
    {
        $authentication = MobileIdAuthentication::newBuilder()
                ->withResult("OK")
                ->withSignatureValueInBase64(TestData::VALID_SIGNATURE_IN_BASE64)
                ->withCertificate(CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_EE))
                ->withSignedHashInBase64(TestData::SIGNED_HASH_IN_BASE64)
                ->withHashType(null)
                ->build();

        $authenticationIdentity = $authentication->constructAuthenticationIdentity();

        $this->assertEquals("MARY ÄNN", $authenticationIdentity->getGivenName());
        $this->assertEquals("O’CONNEŽ-ŠUSLIK TESTNUMBER", $authenticationIdentity->getSurName());
        $this->assertEquals("60001019906", $authenticationIdentity->getIdentityCode());
        $this->assertEquals("EE", $authenticationIdentity->getCountry());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function constructAuthenticationIdentity_withLVCertificate()
    {
        $authentication = MobileIdAuthentication::newBuilder()
                ->withResult("OK")
                ->withSignatureValueInBase64(TestData::VALID_SIGNATURE_IN_BASE64)
                ->withCertificate(CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_LV))
                ->withSignedHashInBase64(TestData::SIGNED_HASH_IN_BASE64)
                ->withHashType(null)
                ->build();

        $authenticationIdentity = $authentication->constructAuthenticationIdentity();

        $this->assertEquals("FORENAME-010117-21234", $authenticationIdentity->getGivenName());
        $this->assertEquals("SURNAME-010117-21234", $authenticationIdentity->getSurName());
        $this->assertEquals("010117-21234", $authenticationIdentity->getIdentityCode());
        $this->assertEquals("LV", $authenticationIdentity->getCountry());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function constructAuthenticationIdentity_withLTCertificate()
    {
        $authentication = MobileIdAuthentication::newBuilder()
                ->withResult("OK")
                ->withSignatureValueInBase64(TestData::VALID_SIGNATURE_IN_BASE64)
                ->withCertificate(CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_LT))
                ->withSignedHashInBase64(TestData::SIGNED_HASH_IN_BASE64)
                ->withHashType(null)
                ->build();

        $authenticationIdentity = $authentication->constructAuthenticationIdentity();

        $this->assertEquals("FORENAMEPNOLT-36009067968", $authenticationIdentity->getGivenName());
        $this->assertEquals("SURNAMEPNOLT-36009067968", $authenticationIdentity->getSurName());
        $this->assertEquals("36009067968", $authenticationIdentity->getIdentityCode());
        $this->assertEquals("LT", $authenticationIdentity->getCountry());
    }

}
