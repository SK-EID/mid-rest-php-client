<?php
namespace sk\mid\tests\rest\integration;
use Exception;
use sk\mid\EST;
use sk\mid\exception\MissingOrInvalidParameterException;
use sk\mid\rest\dao\request\AuthenticationRequest;
use sk\mid\rest\MobileIdRestConnector;
use sk\mid\exception\UnauthorizedException;
use sk\mid\tests\mock\MobileIdRestServiceRequestDummy;
use sk\mid\tests\mock\MobileIdRestServiceResponseDummy;
use sk\mid\tests\mock\TestData;
use sk\mid\tests\mock\SessionStatusPollerDummy;
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

    private function getConnector() : MobileIdRestConnector
    {
        return $this->connector;
    }

    protected function setUp() : void
    {
        $this->connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->build();
    }






    /**
     * @test
     * @throws Exception
     */
    public function authenticate_withDisplayText()
    {
        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();
        $request->setDisplayText("Log into internet banking system");
        MobileIdRestServiceRequestDummy::assertCorrectAuthenticationRequestMade($request);

        $response = $this->getConnector()->authenticate($request);
        $this->assertNotEmpty($response->getSessionId());

        $sessionStatus = SessionStatusPollerDummy::pollSessionStatus($this->connector, $response->getSessionId());
        MobileIdRestServiceResponseDummy::assertAuthenticationPolled($sessionStatus);
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withWrongPhoneNumber_shouldThrowException()
    {
        $request = AuthenticationRequest::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withPhoneNumber("123")
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign(MobileIdRestServiceRequestDummy::calculateMobileIdAuthenticationHash())
            ->withLanguage(EST::asType())
            ->build();

        $this->getConnector()->authenticate($request);
    }

    /**
     * @test
     * @expectedException UnauthorizedException
     */
    public function authenticate_withWrongRelyingPartyUUID_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::WRONG_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->authenticate($request);
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withWrongRelyingPartyName_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::WRONG_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->authenticate($request);
    }

    /**
     * @test
     * @expectedException UnauthorizedException
     */
    public function authenticate_withUnknownRelyingPartyUUID_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::UNKNOWN_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->authenticate($request);
    }

    /**
     * @test
     * @expectedException UnauthorizedException
     */
    public function authenticate_withUnknownRelyingPartyName_shouldThrowException()
    {
        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::UNKNOWN_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->authenticate($request);
    }




}
