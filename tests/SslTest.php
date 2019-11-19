<?php

namespace Sk\SmartId\Tests;

use PHPUnit\Framework\TestCase;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Rest\MobileIdRestConnector;
use Sk\Mid\Tests\Mock\MobileIdRestServiceRequestDummy;
use Sk\Mid\Tests\Mock\TestData;

class SslTest extends TestCase
{
    const AUTHENTICATION_SESSION_PATH = "/authentication/session/{sessionId}";

    private $connector;

    private function getConnector() : MobileIdRestConnector
    {
        return $this->connector;
    }

    /**
     * @test
     */
    public function authenticate_demoEnv_success()
    {
        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID("")
                ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
                ->withHostUrl(TestData::TEST_URL)
                ->withNetworkConnectionConfig("")
                ->withCustomHeaders(array("X-Forwarded-For: 192.10.11.12"))
                ->build();

        $this->connector = $client->getMobileIdConnector();

        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();

        $response = $this->getConnector()->initAuthentication($request);

        self::assertTrue(TRUE);
    }

    /**
     * @test
     */
    public function authenticate_demoEnvUseDemoEnvPublicKeys_success()
    {
        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID("")
                ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
                ->withHostUrl(TestData::TEST_URL)
                ->withNetworkConnectionConfig("")
                ->withDemoEnvPublicKeys()
                ->withCustomHeaders(array("X-Forwarded-For: 192.10.11.12"))
                ->build();

        $this->connector = $client->getMobileIdConnector();

        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();

        $response = $this->getConnector()->initAuthentication($request);

        self::assertTrue(TRUE);
    }


    /**
     * @test
     */
    public function authenticate_demoEnvUseLiveEnvPublicKeys_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID("")
                ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
                ->withHostUrl(TestData::TEST_URL)
                ->withNetworkConnectionConfig("")
                ->withLiveEnvPublicKeys()
                ->withCustomHeaders(array("X-Forwarded-For: 192.10.11.12"))
                ->build();

        $this->connector = $client->getMobileIdConnector();

        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();

        $response = $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function authenticate_demoEnvWithValidPublicKey_success()
    {
        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID("")
                ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
                ->withHostUrl(TestData::TEST_URL)
                ->withNetworkConnectionConfig("")
                ->withSslPublicKeys("sha256//XgrOHbcGDbQJaXjL9ISo+y7bsXAcVOLLEzeeNO6BXDM=")
                ->withCustomHeaders(array("X-Forwarded-For: 192.10.11.12"))
                ->build();

        $this->connector = $client->getMobileIdConnector();

        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();

        $response = $this->getConnector()->initAuthentication($request);

        self::assertTrue(TRUE);
    }

    /**
     * @test
     */
    public function authenticate_demoEnvwithEmptyPublicKey_noResponse()
    {
        $this->expectException(MidInternalErrorException::class);
        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID("")
                ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
                ->withHostUrl(TestData::TEST_URL)
                ->withNetworkConnectionConfig("")
                ->withSslPublicKeys("")
                ->withCustomHeaders(array("X-Forwarded-For: 192.10.11.12"))
                ->build();

        $this->connector = $client->getMobileIdConnector();

        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();

        $response = $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function makeRequestToGoogle_demoPublicKeys_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);
        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID("")
                ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
                ->withHostUrl("https://www.google.com")
                ->withNetworkConnectionConfig("")
                ->withDemoEnvPublicKeys()
                ->withCustomHeaders(array("X-Forwarded-For: 192.10.11.12"))
                ->build();

        $this->connector = $client->getMobileIdConnector();

        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();

        $response = $this->getConnector()->initAuthentication($request);
        self::assertTrue(TRUE);
    }
}
