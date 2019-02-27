<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../mock/TestData.php';
require_once __DIR__ . '/../../ee.sk.mid/rest/dao/request/AuthenticationRequest.php';
require_once __DIR__ . '/../../ee.sk.mid/Language.php';
require_once __DIR__ . '/../../ee.sk.mid/MobileIdAuthenticationHashToSign.php';
require_once __DIR__ . '/../../ee.sk.mid/MobileIdClient.php';
require_once __DIR__ . '/../../ee.sk.mid/exception/NotMIDClientException.php';

class MobileIdAuthenticationIT extends TestCase{


    /**
     * @test
     */
    public function mobileAuthenticateTest()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();


        $resp = self::generateSessionId($client);

        $this->assertEquals(36, strlen($resp->sessionId));
    }

    /**
     * @test
     */
    public function mobileAuthenticate_usingCorrectSessionId_getCorrectSessionStatus()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $sessionIdObject = self::generateSessionId($client);

        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalSessionStatus(
            $sessionIdObject->sessionId,
            '/mid-api/authentication/session/{sessionId}'
        );

        $this->assertEquals(true, !is_null($sessionStatus));
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function mobileAuthenticate_noRelyingPartyName_shouldThrowParameterMissingException()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();


        $resp = self::generateSessionId($client);
    }

    private static function generateSessionId(MobileIdClient $client)
    {
        $authenticationRequest = AuthenticationRequest::newBuilder()
            ->withNationalIdentityNumber(60001019906)
            ->withPhoneNumber("+37200000766")
            ->withLanguage(Language::ENG)
            ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
            ->build();

        $resp = $client->getMobileIdConnector()->authenticate($authenticationRequest);
        return $resp;
    }



}