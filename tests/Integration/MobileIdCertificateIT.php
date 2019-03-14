<?php
namespace Sk\Mid\Tests\integration;
use PHPUnit\Framework\TestCase;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Tests\Mock\MobileIdRestServiceRequestDummy;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Exception\NotMidClientException;

class MobileIdCertificateIT extends TestCase
{

    /**
     * @test
     * @throws \Exception
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

        $resp = $client->getMobileIdConnector()->pullCertificate($certRequest);

        $this->assertEquals('OK', $resp->getResult());
        $this->assertNotNull($resp->getCert());

    }

    /**
     * @test
     */
    public function getCertificate_notMidClient_shouldThrowNotMIDClientException()
    {
        $this->expectException(NotMidClientException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $certRequest = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(60001019928)
            ->withPhoneNumber("+37060000366")
            ->build();

        $client->getMobileIdConnector()->pullCertificate($certRequest);
    }

    /**
     * @test
     */
    public function getCertificate_certificateNotActive_shouldThrowNotMIDClientException()
    {
        $this->expectException(NotMidClientException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $certRequest = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(60001019939)
            ->withPhoneNumber("+37060000266")
            ->build();

        $client->getMobileIdConnector()->pullCertificate($certRequest);
    }


}
