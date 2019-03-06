<?php
namespace sk\mid\tests;
use sk\mid\exception\NotMidClientException;
use sk\mid\rest\MobileIdConnector;
use sk\mid\tests\mock\MobileIdConnectorSpy;
use sk\mid\tests\mock\TestData;

use sk\mid\MobileIdClient;
use sk\mid\rest\MobileIdRestConnector;
use sk\mid\rest\dao\request\CertificateRequest;
use sk\mid\rest\dao\response\CertificateChoiceResponse;
use sk\mid\exception\MissingOrInvalidParameterException;
use sk\mid\exception\MidInternalErrorException;

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

    private function getConnector() : MobileIdConnectorSpy
    {
        return $this->connector;
    }

    protected function setUp() : void
    {
        $this->connector = new MobileIdConnectorSpy();
        $this->connector->setCertificateChoiceResponseToRespond(self::createDummyCertificateChoiceResponse());
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
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
     * @expectedException MissingOrInvalidParameterException
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
     * @expectedException MissingOrInvalidParameterException
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
     * @expectedException MissingOrInvalidParameterException
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
     * @expectedException NotMidClientException
     */
    public function getCertificate_withCertificateNotPresent_shouldThrowException()
    {
        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult("NOT_FOUND");
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     * @expectedException NotMidClientException
     */
    public function getCertificate_withInactiveCertificateFound_shouldThrowException()
    {
        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult("NOT_ACTIVE");
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function getCertificate_withResultMissingInResponse_shouldThrowException()
    {
        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult(null);
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function getCertificate_withResultBlankInResponse_shouldThrowException()
    {
        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult("");
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function getCertificate_withCertificateMissingInResponse_shouldThrowException()
    {
        $this->getConnector()->getCertificateChoiceResponseToRespond()->setCert(null);
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function getCertificate_withCertificateBlankInResponse_shouldThrowException()
    {
        $this->getConnector()->getCertificateChoiceResponseToRespond()->setCert("");
        $this->makeCertificateRequest($this->getConnector());
    }

    private function makeCertificateRequest(MobileIdConnector $connector)
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
        $params = array('result' => 'OK', 'cert' => TestData::AUTH_CERTIFICATE_EE);

        return new CertificateChoiceResponse($params);

    }


}
