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
     * @return mixed
     */
    private static function getSessionId()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();


        $authenticationRequest = AuthenticationRequest::newBuilder()
            ->withNationalIdentityNumber(60001019906)
            ->withPhoneNumber("+37200000766")
            ->withLanguage(Language::ENG)
            ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
            ->build();

        $resp = $client->getMobileIdConnector()->authenticate($authenticationRequest);
        return $resp;
    }

    /**
     * @test
     */
    public function mobileAuthenticateTest()
    {
        $resp = self::getSessionId();

        $this->assertEquals(36, strlen($resp->sessionId));
    }

    /**
     * @test
     */
    public function mobileAuthenticate_notMidClient_shouldThrowNotMidClient()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();


        $authenticationRequest = AuthenticationRequest::newBuilder()
            ->withNationalIdentityNumber(60001019928)
            ->withLanguage(Language::ENG)
            ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
            ->build();

        $resp = $client->getMobileIdConnector()->authenticate($authenticationRequest);

        $this->assertEquals(36, strlen($resp->sessionId));
    }


}