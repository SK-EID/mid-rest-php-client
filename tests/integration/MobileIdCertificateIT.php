<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../mock/MobileIdRestServiceRequestDummy.php';
require_once __DIR__ . '/../mock/TestData.php';
require_once __DIR__ . '/../../ee.sk.mid/MobileIdClient.php';

class MobileIdCertificateIT extends TestCase
{
    private $client;

    /**
     * @test
     */
    public function getCertificateTest()
    {

        $this->client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();


        $certRequest = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(60001019906)
            ->withPhoneNumber("+37200000766")
            ->build();

        $resp = $this->client->getMobileIdConnector()->getCertificate($certRequest);

        echo 'result' . $resp->result;
        echo 'cert:' . $resp->cert;

        $this->assertNotNull($resp->cert);

    //    $certificate = MobileIdRestServiceRequestDummy::getCertificate($this->client);
//        MobileIdRestServiceRequestDummy::assertCertificateCreated($certificate);
    }


}