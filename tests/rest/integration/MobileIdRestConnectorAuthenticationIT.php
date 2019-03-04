<?php
require_once __DIR__ . '/../../../ee.sk.mid/rest/MobileIdRestConnector.php';
require_once __DIR__ . '/../../../ee.sk.mid/exception/UnAuthorizedException.php';
require_once __DIR__ . '/../../mock/MobileIdRestServiceRequestDummy.php';
require_once __DIR__ . '/../../mock/MobileIdRestServiceResponseDummy.php';
require_once __DIR__ . '/../../mock/TestData.php';
require_once __DIR__ . '/../../mock/SessionStatusPollerDummy.php';
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 1:19 PM
 */
class MobileIdRestConnectorAuthenticationIT extends TestCase
{
    const AUTHENTICATION_SESSION_PATH = "/authentication/session/{sessionId}";

    private $connector;

    protected function setUp()
    {
        $this->connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->build();
    }

    /**
     * @test
     */
    public function authenticate_withDisplayText()
    {
        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();
        $request->setDisplayText("Log into internet banking system");
        MobileIdRestServiceRequestDummy::assertCorrectAuthenticationRequestMade($request);

        $response = $this->connector->authenticate($request);
        assert(!is_null($response->getSessionId()) && !empty($response->getSessionId()));

        $sessionStatus = SessionStatusPollerDummy::pollSessionStatus($this->connector, $response->getSessionId(), TestData::AUTHENTICATION_SESSION_PATH);
        MobileIdRestServiceResponseDummy::assertAuthenticationPolled($sessionStatus);
    }

    /**
     * @test
     * @expectedException UnAuthorizedException
     */
    public function authenticate_withWrongPhoneNumber_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::WRONG_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException UnAuthorizedException
     */
    public function authenticate_withWrongNationalIdentityNumber_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::WRONG_NAT_IDENTITY
        );
        $this->connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException UnAuthorizedException
     */
    public function authenticate_withWrongRelyingPartyUUID_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::WRONG_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException UnAuthorizedException
     */
    public function authenticate_withWrongRelyingPartyName_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::WRONG_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException UnAuthorizedException
     */
    public function authenticate_withUnknownRelyingPartyUUID_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::UNKNOWN_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException UnAuthorizedException
     */
    public function authenticate_withUnknownRelyingPartyName_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::UNKNOWN_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->connector->authenticate($request);
    }




}
