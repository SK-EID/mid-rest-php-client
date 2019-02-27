<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../mock/MobileIdRestServiceRequestDummy.php';
require_once __DIR__ . '/../mock/TestData.php';
require_once __DIR__ . '/../../ee.sk.mid/MobileIdClient.php';
require_once __DIR__ . '/../../ee.sk.mid/exception/NotMIDClientException.php';

class MobileIdCertificateIT extends TestCase
{

    /**
     * @test
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

        $this->assertEquals('OK', $resp->result);
        $this->assertNotNull($resp->cert);

    //    $certificate = MobileIdRestServiceRequestDummy::getCertificate($this->client);
//        MobileIdRestServiceRequestDummy::assertCertificateCreated($certificate);
    }

    /**
     * @test
     * @expectedException NotMIDClientException
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
     * @expectedException NotMIDClientException
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