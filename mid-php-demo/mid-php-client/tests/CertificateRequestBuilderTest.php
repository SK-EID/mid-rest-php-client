<?php

use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 4:38 PM
 */
class CertificateRequestBuilderTest extends TestCase
{
    private $connector;

    protected function setUp()
    {
        $this->connector = new MobileIdConnectorSpy();
        $this->connector->setCertificateChoiceResponseToRespond(self::createDummyCertificateChoiceResponse());
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function getCertificate_withoutRelyingPartyUUID_shouldThrowException()
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->getCertificate($request);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function getCertificate_withoutRelyingPartyName_shouldThrowException()
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->build();

        $connector->getCertificate($request);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function getCertificate_withoutPhoneNumber_shouldThrowException()
    {
        $request = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->getCertificate($request);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function getCertificate_withoutNationalIdentityNumber_shouldThrowException()
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->getCertificate($request);
    }

    /**
     * @test
     * @expectedException CertificateNotPresentException
     */
    public function getCertificate_withCertificateNotPresent_shouldThrowException()
    {
        $this->connector->getCertificateChoiceResponseToRespond()->setResult("NOT_FOUND");
        $this->makeCertificateRequest($this->connector);
    }

    /**
     * @test
     * @expectedException ExpiredException
     */
    public function getCertificate_withInactiveCertificateFound_shouldThrowException()
    {
        $this->connector->getCertificateChoiceResponseToRespond()->setResult("NOT_ACTIVE");
        $this->makeCertificateRequest($this->connector);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function getCertificate_withResultMissingInResponse_shouldThrowException()
    {
        $this->connector->getCertificateChoiceResponseToRespond()->setResult(null);
        $this->makeCertificateRequest($this->connector);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function getCertificate_withResultBlankInResponse_shouldThrowException()
    {
        $this->connector->getCertificateChoiceResponseToRespond()->setResult("");
        $this->makeCertificateRequest($this->connector);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function getCertificate_withCertificateMissingInResponse_shouldThrowException()
    {
        $this->connector->getCertificateChoiceResponseToRespond()->setCert(null);
        $this->makeCertificateRequest($this->connector);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function getCertificate_withCertificateBlankInResponse_shouldThrowException()
    {
        $this->connector->getCertificateChoiceResponseToRespond()->setCert("");
        $this->makeCertificateRequest($this->connector);
    }

    private function makeCertificateRequest($connector)
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $response = $connector->getCertificate($request);
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::LOCALHOST_URL)
            ->build();
        $client->createMobileIdCertificate($response);
    }

    private static function createDummyCertificateChoiceResponse()
    {
        $certificateChoiceResponse = new CertificateChoiceResponse();
        $certificateChoiceResponse->setResult("OK");
        $certificateChoiceResponse->setCert(TestData::AUTH_CERTIFICATE_EE);
        return $certificateChoiceResponse;
    }


}