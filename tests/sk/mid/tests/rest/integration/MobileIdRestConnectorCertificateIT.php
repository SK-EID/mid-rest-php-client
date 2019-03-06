<?php
namespace sk\mid\tests\rest\integration;
use Exception;
use sk\mid\exception\NotMidClientException;
use sk\mid\rest\dao\request\CertificateRequest;
use sk\mid\tests\mock\MobileIdRestServiceRequestDummy;
use sk\mid\exception\UnauthorizedException;
use sk\mid\tests\mock\TestData;
use sk\mid\rest\MobileIdRestConnector;

use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 1:42 PM
 */
class MobileIdRestConnectorCertificateIT extends TestCase
{

    /** @var MobileIdRestConnector $connector */
    private $connector;

    private function getConnector() : MobileIdRestConnector
    {
        return $this->connector;
    }

    protected function setUp() : void
    {
        $this->connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function getCertificate()
    {
        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        MobileIdRestServiceRequestDummy::assertCorrectCertificateRequestMade($request);

        $response = $this->getConnector()->getCertificate($request);

        assert(!is_null($response));
        try {
            $this->assertEquals("OK", $response->getResult());
        } catch (Exception $e) {
        }
        assert(!is_null($response->getCert()) && !empty($response->getCert()));
    }

    /**
     * @test
     * @expectedException NotMidClientException
     */
    public function getCertificate_withWrongPhoneNumber_shouldThrowException()
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::WRONG_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $this->getConnector()->getCertificate($request);
    }

    /**
     * @test
     * @expectedException NotMidClientException
     */
    public function getCertificate_withWrongNationalIdentityNumber_shouldThrowException()
    {
        $request = CertificateRequest::newBuilder()
                ->withPhoneNumber(TestData::VALID_PHONE)
                ->withNationalIdentityNumber(TestData::WRONG_NAT_IDENTITY)
                ->build();


        $this->getConnector()->getCertificate($request);
    }

    /**
     * @test
     * @expectedException UnauthorizedException
     */
    public function getCertificate_withWrongRelyingPartyUUID_shouldThrowException()
    {
        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::WRONG_RELYING_PARTY_UUID)
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
