<?php
require_once __DIR__ . '/mock/MobileIdConnectorSpy.php';
require_once __DIR__ . '/mock/SessionStatusDummy.php';
require_once __DIR__ . '/mock/TestData.php';

require_once __DIR__ . '/../../../src/sk/mid/exception/MissingOrInvalidParameterException.php';
require_once __DIR__ . '/../../../src/sk/mid/exception/MidSessionTimeoutException.php';
require_once __DIR__ . '/../../../src/sk/mid/exception/MidInternalErrorException.php';

require_once __DIR__ . '/../../../src/sk/mid/Language.php';
require_once __DIR__ . '/../../../src/sk/mid/MobileIdAuthenticationHashToSign.php';
require_once __DIR__ . '/../../../src/sk/mid/MobileIdClient.php';
require_once __DIR__ . '/../../../src/sk/mid/rest/MobileIdRestConnector.php';
require_once __DIR__ . '/../../../src/sk/mid/rest/SessionStatusPoller.php';
require_once __DIR__ . '/../../../src/sk/mid/rest/dao/SessionSignature.php';
require_once __DIR__ . '/../../../src/sk/mid/rest/dao/SessionStatus.php';
require_once __DIR__ . '/../../../src/sk/mid/rest/dao/request/AuthenticationRequest.php';
require_once __DIR__ . '/../../../src/sk/mid/rest/dao/response/AuthenticationResponse.php';


use PHPUnit\Framework\TestCase;

class AuthenticationRequestBuilderTest extends TestCase
{
    /** @var MobileIdConnectorSpy $connector */
    private $connector;

    protected function setUp()
    {
        $this->connector = new MobileIdConnectorSpy();
        $this->connector->setAuthenticationResponseToRespond(new AuthenticationResponse( array('sessionId' => TestData::SESSION_ID)));
        $this->connector->setSessionStatusToRespond(self::createDummyAuthenticationSessionStatus());
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withoutRelyingPartyUUID_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign($mobileAuthenticationHash)
            ->withLanguage(EST::asType())
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->build();

        $connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withoutRelyingPartyName_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign($mobileAuthenticationHash)
            ->withLanguage(EST::asType())
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->build();
        $connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withoutPhoneNumber_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign($mobileAuthenticationHash)
            ->withLanguage(EST::asType())
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withoutNationalIdentityNumber_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withHashToSign($mobileAuthenticationHash)
            ->withLanguage(EST::asType())
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withoutHashToSign_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withLanguage(EST::asType())
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withoutLanguage_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign($mobileAuthenticationHash)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException MidSessionTimeoutException
     */
    public function authenticate_withTimeout_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createTimeoutSessionStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function authenticate_withResponseRetrievingError_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createResponseRetrievingErrorStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException NotMidClientException
     */
    public function authenticate_withNotMIDClient_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createNotMIDClientStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
 * @test
 * @expectedException MidSessionTimeoutException
 */
    public function authenticate_withMSSPTransactionExpired_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createMSSPTransactionExpiredStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException UserCancellationException
     */
    public function authenticate_withUserCancellation_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createUserCancellationStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function authenticate_withMIDNotReady_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createMIDNotReadyStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException PhoneNotAvailableException
     */
    public function authenticate_withSimNotAvailable_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createSimNotAvailableStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException DeliveryException
     */
    public function authenticate_withDeliveryError_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createDeliveryErrorStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException DeliveryException
     */
    public function authenticate_withInvalidCardResponse_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createInvalidCardResponseStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function authenticate_withResultMissingInResponse_shouldThrowException()
    {
        $this->connector->getSessionStatusToRespond()->setResult(null);
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function authenticate_withResultBlankInResponse_shouldThrowException()
    {
        $this->connector->getSessionStatusToRespond()->setResult("");
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withCertificateBlankInResponse_shouldThrowException()
    {
        $this->connector->getSessionStatusToRespond()->setCert("");
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException MissingOrInvalidParameterException
     */
    public function authenticate_withCertificateMissingInResponse_shouldThrowException()
    {
        $this->connector->getSessionStatusToRespond()->setCert(null);
        $this->makeAuthenticationRequest($this->connector);
    }
    
    private function makeAuthenticationRequest(MobileIdConnector $connector)
    {
        $authenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();

        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign($authenticationHash)
            ->withLanguage(EST::asType())
            ->build();

        $response = $connector->authenticate($request);

        $poller = new SessionStatusPoller($connector);
        $sessionStatus = $poller->fetchFinalSessionStatus($response->getSessionId());

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::LOCALHOST_URL)
            ->build();

        $client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    }

    private static function createDummyAuthenticationSessionStatus()
    {
        $signature = MobileIdSignature::newBuilder()
                ->withValueInBase64("c2FtcGxlIHNpZ25hdHVyZQ0K")
                ->withAlgorithmName("sha512WithRSAEncryption")
                ->build();

        $sessionStatus = new SessionStatus();
        $sessionStatus->setState("COMPLETE");
        $sessionStatus->setResult("OK");
        $sessionStatus->setSignature($signature);
        $sessionStatus->setCert(TestData::AUTH_CERTIFICATE_EE);
        return $sessionStatus;
    }
}