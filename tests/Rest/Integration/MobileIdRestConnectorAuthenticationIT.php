<?php
namespace Sk\Mid\Tests\Rest\Integration;
use Sk\Mid\Language\EST;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\MobileIdRestConnector;
use Sk\Mid\Exception\UnauthorizedException;
use Sk\Mid\Tests\Mock\MobileIdRestServiceRequestDummy;
use Sk\Mid\Tests\Mock\MobileIdRestServiceResponseDummy;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Tests\Mock\SessionStatusPollerDummy;
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
     */
    public function authenticate_withDisplayText()
    {
        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();
        $request->setDisplayText("Log into internet banking system");
        MobileIdRestServiceRequestDummy::assertCorrectAuthenticationRequestMade($request);

        $response = $this->getConnector()->initAuthentication($request);
        $this->assertNotEmpty($response->getSessionId());

        $sessionStatus = SessionStatusPollerDummy::pollSessionStatus($this->connector, $response->getSessionId());
        MobileIdRestServiceResponseDummy::assertAuthenticationPolled($sessionStatus);
    }

    /**
     * @test
     */
    public function authenticate_withWrongPhoneNumber_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $request = AuthenticationRequest::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withPhoneNumber("123")
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign(MobileIdRestServiceRequestDummy::calculateMobileIdAuthenticationHash())
            ->withLanguage(EST::asType())
            ->build();

        $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function authenticate_withWrongRelyingPartyUUID_shouldThrowException()
    {
        $this->expectException(UnauthorizedException::class);

        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::WRONG_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function authenticate_withWrongRelyingPartyName_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::WRONG_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function authenticate_withUnknownRelyingPartyUUID_shouldThrowException()
    {
        $this->expectException(UnauthorizedException::class);

        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::UNKNOWN_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function authenticate_withUnknownRelyingPartyName_shouldThrowException()
    {
        $this->expectException(UnauthorizedException::class);

        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::UNKNOWN_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->initAuthentication($request);
    }




}
