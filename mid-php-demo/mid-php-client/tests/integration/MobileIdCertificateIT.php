<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../mock/TestData.php';

class MobileIdCertificateIT extends TestCase
{
    private $client;

    protected function setUp()
    {

        $this->client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $certificate = MobileIdRestServiceRequestDummy::getCertificate($this->client);
        MobileIdRestServiceRequestDummy::assertCertificateCreated($certificate);
    }


}