<?php
namespace Sk\Mid\Tests;
use Sk\Mid\Exception\NotMidClientException;
use Sk\Mid\Rest\MobileIdConnector;
use Sk\Mid\Tests\Mock\MobileIdConnectorSpy;
use Sk\Mid\Tests\Mock\TestData;

use Sk\Mid\MobileIdClient;
use Sk\Mid\Rest\MobileIdRestConnector;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Rest\Dao\Response\CertificateResponse;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\Exception\MidInternalErrorException;

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
     */
    public function getCertificate_withoutRelyingPartyUUID_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withoutRelyingPartyName_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->build();

        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withoutPhoneNumber_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $request = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withoutNationalIdentityNumber_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withCertificateNotPresent_shouldThrowException()
    {
        $this->expectException(NotMidClientException::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult("NOT_FOUND");
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     */
    public function getCertificate_withInactiveCertificateFound_shouldThrowException()
    {
        $this->expectException(NotMidClientException::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult("NOT_ACTIVE");
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     */
    public function getCertificate_withResultMissingInResponse_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult(null);
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     */
    public function getCertificate_withResultBlankInResponse_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult("");
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     */
    public function getCertificate_withCertificateMissingInResponse_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setCert(null);
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     */
    public function getCertificate_withCertificateBlankInResponse_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setCert("");
        $this->makeCertificateRequest($this->getConnector());
    }

    private function makeCertificateRequest(MobileIdConnector $connector)
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $response = $connector->pullCertificate($request);
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

        return new CertificateResponse($params);

    }


}
