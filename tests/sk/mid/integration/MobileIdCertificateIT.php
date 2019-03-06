<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../mock/MobileIdRestServiceRequestDummy.php';
require_once __DIR__ . '/../mock/TestData.php';
require_once __DIR__ . '/../../../../src/sk/mid/MobileIdClient.php';
require_once __DIR__ . '/../../../../src/sk/mid/exception/NotMidClientException.php';

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