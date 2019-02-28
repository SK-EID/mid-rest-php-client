<?php
require_once __DIR__ . '/mock/MobileIdConnectorSpy.php';
require_once __DIR__ . '/mock/SessionStatusDummy.php';
require_once __DIR__ . '/mock/TestData.php';

require_once __DIR__ . '/../ee.sk.mid/exception/CertificateNotPresentException.php';
require_once __DIR__ . '/../ee.sk.mid/exception/CertificateRevokedException.php';
require_once __DIR__ . '/../ee.sk.mid/exception/ParameterMissingException.php';
require_once __DIR__ . '/../ee.sk.mid/exception/SessionTimeoutException.php';
require_once __DIR__ . '/../ee.sk.mid/exception/TechnicalErrorException.php';

require_once __DIR__ . '/../ee.sk.mid/Language.php';
require_once __DIR__ . '/../ee.sk.mid/MobileIdAuthenticationHashToSign.php';
require_once __DIR__ . '/../ee.sk.mid/MobileIdClient.php';
require_once __DIR__ . '/../ee.sk.mid/rest/MobileIdRestConnector.php';
require_once __DIR__ . '/../ee.sk.mid/rest/SessionStatusPoller.php';
require_once __DIR__ . '/../ee.sk.mid/rest/dao/SessionSignature.php';
require_once __DIR__ . '/../ee.sk.mid/rest/dao/SessionStatus.php';
require_once __DIR__ . '/../ee.sk.mid/rest/dao/request/AuthenticationRequest.php';
require_once __DIR__ . '/../ee.sk.mid/rest/dao/response/AuthenticationResponse.php';


use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 3:10 PM
 */

class AuthenticationRequestBuilderTest extends TestCase
{
    private $connector;

    protected function setUp()
    {
        $this->connector = new MobileIdConnectorSpy();
        $this->connector->setAuthenticationResponseToRespond(new AuthenticationResponse(TestData::SESSION_ID));
        $this->connector->setSessionStatusToRespond(self::createDummyAuthenticationSessionStatus());
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function authenticate_withoutRelyingPartyUUID_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign($mobileAuthenticationHash)
            ->withLanguage(Language::EST)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->build();

        $connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function authenticate_withoutRelyingPartyName_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign($mobileAuthenticationHash)
            ->withLanguage(Language::EST)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::LOCALHOST_URL)
            ->build();
        $connector->authenticate($request);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function authenticate_withoutPhoneNumber_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign($mobileAuthenticationHash)
            ->withLanguage(Language::EST)
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
     * @expectedException ParameterMissingException
     */
    public function authenticate_withoutNationalIdentityNumber_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withHashToSign($mobileAuthenticationHash)
            ->withLanguage(Language::EST)
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
     * @expectedException ParameterMissingException
     */
    public function authenticate_withoutHashToSign_shouldThrowException()
    {
        $mobileAuthenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withLanguage(Language::EST)
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
     * @expectedException ParameterMissingException
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
     * @expectedException SessionTimeoutException
     */
    public function authenticate_withTimeout_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createTimeoutSessionStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException ResponseRetrievingException
     */
    public function authenticate_withResponseRetrievingError_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createResponseRetrievingErrorStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException NotMIDClientException
     */
    public function authenticate_withNotMIDClient_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createNotMIDClientStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
 * @test
 * @expectedException CertificateRevokedException
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
     * @expectedException MIDNotReadyException
     */
    public function authenticate_withMIDNotReady_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createMIDNotReadyStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException SimNotAvailableException
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
     * @expectedException InvalidCardResponseException
     */
    public function authenticate_withInvalidCardResponse_shouldThrowException()
    {
        $this->connector->setSessionStatusToRespond(SessionStatusDummy::createInvalidCardResponseStatus());
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function authenticate_withResultMissingInResponse_shouldThrowException()
    {
        $this->connector->getSessionStatusToRespond()->setResult(null);
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function authenticate_withResultBlankInResponse_shouldThrowException()
    {
        $this->connector->getSessionStatusToRespond()->setResult("");
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function authenticate_withCertificateBlankInResponse_shouldThrowException()
    {
        $this->connector->getSessionStatusToRespond()->setCert("");
        $this->makeAuthenticationRequest($this->connector);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function authenticate_withCertificateMissingInResponse_shouldThrowException()
    {
        $this->connector->getSessionStatusToRespond()->setCert(null);
        $this->makeAuthenticationRequest($this->connector);
    }
    
    private function makeAuthenticationRequest($connector)
    {
        $authenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();

        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign($authenticationHash)
            ->withLanguage(Language::EST)
            ->build();

        $response = $connector->authenticate($request);

        $poller = new SessionStatusPoller($connector);
        $sessionStatus = $poller->fetchFinalSessionStatus($response->getSessionId(), TestData::AUTHENTICATION_SESSION_PATH);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::LOCALHOST_URL)
            ->build();

        $client->createMobileIdAuthentication($sessionStatus, $authenticationHash->getHashInBase64(), $authenticationHash->getHashType());
    }

    private static function createDummyAuthenticationSessionStatus()
    {
        $signature = new SessionSignature();
        $signature->setValue("c2FtcGxlIHNpZ25hdHVyZQ0K");
        $signature->setAlgorithm("sha512WithRSAEncryption");
        $sessionStatus = new SessionStatus();
        $sessionStatus->setState("COMPLETE");
        $sessionStatus->setResult("OK");
        $sessionStatus->setSignature($signature);
        $sessionStatus->setCert(TestData::AUTH_CERTIFICATE_EE);
        return $sessionStatus;
    }
}