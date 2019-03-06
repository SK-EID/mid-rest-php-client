<?php
namespace sk\mid\tests\integration;

use Exception;
use PHPUnit\Framework\TestCase;
use sk\mid\rest\dao\request\CertificateRequest;
use sk\mid\tests\mock\MobileIdRestServiceRequestDummy;
use sk\mid\tests\mock\TestData;
use sk\mid\MobileIdClient;
use sk\mid\exception\NotMidClientException;

class MobileIdCertificateIT extends TestCase
{

    /**
     * @test
     * @throws Exception
     */
    public function getCertificateTest()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();


        $certRequest = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(60001019906)
            ->withPhoneNumber("+37200000766")
            ->build();

        $resp = $client->getMobileIdConnector()->getCertificate($certRequest);

        $this->assertEquals('OK', $resp->getResult());
        $this->assertNotNull($resp->getCert());

    }

    /**
     * @test
     * @expectedException NotMidClientException
     */
    public function getCertificate_notMidClient_shouldThrowNotMIDClientException()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $certRequest = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(60001019928)
            ->withPhoneNumber("+37060000366")
            ->build();

        $client->getMobileIdConnector()->getCertificate($certRequest);
    }

    /**
     * @test
     * @expectedException NotMidClientException
     */
    public function getCertificate_certificateNotActive_shouldThrowNotMIDClientException()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $certRequest = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(60001019939)
            ->withPhoneNumber("+37060000266")
            ->build();

        $client->getMobileIdConnector()->getCertificate($certRequest);
    }


}
