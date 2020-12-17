<?php

namespace Sk\SmartId\Tests;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
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
                ->withHostUrl(TestData::DEMO_HOST_URL)
                ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
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
                ->withHostUrl(TestData::DEMO_HOST_URL)
                ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH )
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
                ->withHostUrl(TestData::DEMO_HOST_URL)
                ->withSslPinnedPublicKeys( TestData::SOME_OTHER_HOST_PUBLIC_KEY_HASH )
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
                ->withHostUrl(TestData::DEMO_HOST_URL)
                ->withSslPinnedPublicKeys(TestData::DEMO_HOST_PUBLIC_KEY_HASH)
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
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("You need to set hash value(s) of trusted API HOST SSL public keys by calling withSslPinnedPublicKeys()");

        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID("")
                ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
                ->withHostUrl(TestData::DEMO_HOST_URL)
                ->withSslPinnedPublicKeys("")
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
                ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH )
                ->build();

        $this->connector = $client->getMobileIdConnector();

        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();

        $response = $this->getConnector()->initAuthentication($request);
        self::assertTrue(TRUE);
    }
}
