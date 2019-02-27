<?php
require_once __DIR__ . '/../../mock/MobileIdRestServiceRequestDummy.php';
require_once __DIR__ . '/../../mock/MobileIdRestServiceRequestDummy.php';
require_once __DIR__ . '/../../mock/TestData.php';
require_once __DIR__ . '/../../mock/TestData.php';
require_once __DIR__ . '/../../../ee.sk.mid/rest/MobileIdRestConnector.php';
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 1:42 PM
 */
class MobileIdRestConnectorCertificateIT extends TestCase
{

    private $connector;

    protected function setUp()
    {
        $this->connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();
    }

    /**
     * @test
     */
    public function getCertificate()
    {
        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        MobileIdRestServiceRequestDummy::assertCorrectCertificateRequestMade($request);

        $response = $this->connector->getCertificate($request);

        assert(!is_null($response));
        try {
            $this->assertEquals("OK", $response->result);
        } catch (Exception $e) {
        }
        assert(!is_null($response->cert) && !empty($response->cert));
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function getCertificate_withWrongPhoneNumber_shouldThrowException()
    {
        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::WRONG_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);

        $this->connector->getCertificate($request);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function getCertificate_withWrongNationalIdentityNumber_shouldThrowException()
    {
        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::WRONG_NAT_IDENTITY);

        $this->connector->getCertificate($request);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function getCertificate_withWrongRelyingPartyUUID_shouldThrowException()
    {
        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID("wrong_UUID")
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        $connector->getCertificate($request);
    }

    /**
     * @test
     * @expectedException UnauthorizedException
     */
    public function getCertificate_withWrongRelyingPartyName_shouldThrowException()
    {
        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName("wrong name")
            ->build();

        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        $connector->getCertificate($request);
    }

    /**
     * @test
     * @expectedException UnauthorizedException
     */
    public function getCertificate_withUnknownRelyingPartyUUID_shouldThrowException()
    {
        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::UNKNOWN_RELYING_PARTY_NAME)
            ->build();

        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        $connector->getCertificate($request);
    }

    /**
     * @test
     * @expectedException UnauthorizedException
     */
    public function getCertificate_withUnknownRelyingPartyName_shouldThrowException()
    {
        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::UNKNOWN_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        $connector->getCertificate($request);
    }








}