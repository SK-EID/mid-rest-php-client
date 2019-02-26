<?php
require_once __DIR__ . '/../ee.sk.mid/AuthenticationResponseValidator.php';
require_once __DIR__ . '/mock/TestData.php.php';

use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 3:46 PM
 */
class AuthenticationResponseValidatorTest extends TestCase
{

    private $validator;

    protected function setUp()
    {
        $this->validator = new AuthenticationResponseValidator();
    }

    /**
     * @test
     * @throws Exception
     */
    public function validate_whenRSA_shouldReturnValidAuthenticationResult()
    {
        $authentication = $this->createValidMobileIdAuthentication();
        $authenticationResult = $this->validator->validate($authentication);

        $this->assertEquals(true, $authenticationResult->isValid());
        $this->assertEquals(true, count($authenticationResult->getErrors()) == 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function validate_whenECC_shouldReturnValidAuthenticationResult()
    {
        $authentication = $this->createMobileIdAuthenticationWithECC();
        $authenticationResult = $this->validator->validate($authentication);

        $this->assertEquals(true, $authenticationResult->isValid());
        $this->assertEquals(true, count($authenticationResult->getErrors()) == 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function validate_whenResultLowerCase_shouldReturnValidAuthenticationResult()
    {
        $authentication = MobileIdAuthentication::newBuilder()
            ->withResult("OK")
            ->withSignatureValueInBase64(TestData::VALID_SIGNATURE_IN_BASE64)
            ->withCertificate(CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_EE))
            ->withSignedHashInBase64(TestData::SIGNED_HASH_IN_BASE64)
            ->withHashType(HashType::SHA512)
            ->build();

        $authenticationResult = $this->validator->validate($authentication);
        $this->assertEquals(true, $authenticationResult->isValid());
        $this->assertEquals(true, count($authenticationResult->getErrors()) == 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function validate_whenResultNotOk_shouldReturnInvalidAuthenticationResult()
    {
        $authentication = $this->createMobileIdAuthenticationWithInvalidResult();
        $authenticationResult = $this->validator->validate($authentication);

        $this->assertEquals(false, $authenticationResult->isValid());
        $this->assertEquals(true, in_array("Response result verification failed", $authenticationResult->getErrors()));
    }

    /**
     * @test
     * @throws Exception
     */
    public function validate_shouldReturnValidIdentity()
    {
        $authentication = $this->createValidMobileIdAuthentication();
        $authenticationResult = $this->validator->validate($authentication);

        $this->assertEquals("MARY ÄNN", $authenticationResult->getAuthenticationIdentity()->getGivenName());
        $this->assertEquals("O’CONNEŽ-ŠUSLIK TESTNUMBER", $authenticationResult->getAuthenticationIdentity()->getSurName());
        $this->assertEquals("60001019906", $authenticationResult->getAuthenticationIdentity()->getIdentityCode());
        $this->assertEquals("EE", $authenticationResult->getAuthenticationIdentity()->getCountry());
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function validate_whenCertificateIsNull_shouldThrowException()
    {
        $authentication = MobileIdAuthentication::newBuilder()
            ->withResult("OK")
            ->withSignatureValueInBase64(TestData::VALID_SIGNATURE_IN_BASE64)
            ->withCertificate(null)
            ->withSignedHashInBase64(TestData::SIGNED_HASH_IN_BASE64)
            ->withHashType(HashType::SHA512)
            ->build();

        $this->validator->validate($authentication);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function validate_whenHashTypeIsNull_shouldThrowException()
    {
        $authentication = MobileIdAuthentication::newBuilder()
            ->withResult("OK")
            ->withSignatureValueInBase64(TestData::VALID_SIGNATURE_IN_BASE64)
            ->withCertificate(CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_EE))
            ->withSignedHashInBase64(TestData::SIGNED_HASH_IN_BASE64)
            ->withHashType(null)
            ->build();

        $this->validator->validate($authentication);
    }

    /**
     * @test
     * @throws Exception
     */
    public function constructAuthenticationIdentity_withEECertificate()
    {
        $cerificateEe = CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_EE);
        $authenticationIdentity = $this->validator->constructAuthenticationIdentity($cerificateEe);

        $this->assertEquals("MARY ÄNN", $authenticationIdentity->getGivenName());
        $this->assertEquals("O’CONNEŽ-ŠUSLIK TESTNUMBER", $authenticationIdentity->getSurName());
        $this->assertEquals("60001019906", $authenticationIdentity->getIdentityCode());
        $this->assertEquals("EE", $authenticationIdentity->getCountry());
    }

    /**
     * @test
     * @throws Exception
     */
    public function constructAuthenticationIdentity_withLVCertificate()
    {
        $certificateLv = CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_LV);
        $authenticationIdentity = $this->validator->constructAuthenticationIdentity($certificateLv);

        $this->assertEquals("FORENAME-010117-21234", $authenticationIdentity->getGivenName());
        $this->assertEquals("SURNAME-010117-21234", $authenticationIdentity->getSurName());
        $this->assertEquals("010117-21234", $authenticationIdentity->getIdentityCode());
        $this->assertEquals("LV", $authenticationIdentity->getCountry());
    }

    /**
     * @test
     * @throws Exception
     */
    public function constructAuthenticationIdentity_withLTCertificate()
    {
        $certificateLt = CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_LT);
        $authenticationIdentity = $this->validator->constructAuthenticationIdentity($certificateLt);

        $this->assertEquals("FORENAMEPNOLT-36009067968", $authenticationIdentity->getAuthenticationIdentity()->getGivenName());
        $this->assertEquals("SURNAMEPNOLT-36009067968", $authenticationIdentity->getAuthenticationIdentity()->getSurName());
        $this->assertEquals("36009067968", $authenticationIdentity->getAuthenticationIdentity()->getIdentityCode());
        $this->assertEquals("LT", $authenticationIdentity->getAuthenticationIdentity()->getCountry());
    }

    private function createValidMobileIdAuthentication()
    {
        return $this->createMobileIdAuthentication("OK", TestData::VALID_SIGNATURE_IN_BASE64);
    }

    private function createMobileIdAuthenticationWithInvalidResult()
    {
        return $this->createMobileIdAuthentication("NOT OK", TestData::VALID_SIGNATURE_IN_BASE64);
    }

    private function createMobileIdAuthentication($result, $signatureInBase64)
    {
        return MobileIdAuthentication::newBuilder()
            ->withResult($result)
            ->withSignatureValueInBase64($signatureInBase64)
            ->withCertificate(CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_EE))
            ->withSignedHashInBase64(TestData::SIGNED_HASH_IN_BASE64)
            ->withHashType(HashType::SHA512)
            ->build();
    }

    private function createMobileIdAuthenticationWithECC()
    {
        return MobileIdAuthentication::newBuilder()
            ->withResult("OK")
            ->withSignatureValueInBase64(TestData::VALID_ECC_SIGNATURE_IN_BASE64)
            ->withCertificate(CertificateParser::parseX509Certificate(TestData::ECC_CERTIFICATE))
            ->withSignedHashInBase64(TestData::SIGNED_ECC_HASH_IN_BASE64)
            ->withHashType(HashType::SHA512)
            ->build();
    }






}