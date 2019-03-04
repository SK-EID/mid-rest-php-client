<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../ee.sk.mid/util/Logger.php';

require_once __DIR__ . '/../mock/TestData.php';
require_once __DIR__ . '/../../ee.sk.mid/rest/dao/request/AuthenticationRequest.php';
require_once __DIR__ . '/../../ee.sk.mid/Language.php';
require_once __DIR__ . '/../../ee.sk.mid/MobileIdAuthenticationHashToSign.php';
require_once __DIR__ . '/../../ee.sk.mid/MobileIdClient.php';
require_once __DIR__ . '/../../ee.sk.mid/exception/NotMIDClientException.php';

class MobileIdAuthenticationIT extends TestCase
{

    /**
     * @test
     * @throws Exception
     */
    public function mobileAuthenticateTest()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();


        $resp = self::generateSessionId($client);

        $this->assertEquals(36, strlen($resp->getSessionId()));
    }

    /**
     * @test
     * @throws Exception
     */
    public function mobileAuthenticate_usingCorrectSessionId_getCorrectSessionStatus()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $authenticationResponse = self::generateSessionId($client);

        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($authenticationResponse->getSessionId());

        $this->assertThat($sessionStatus, $this->logicalNot($this->isNull()));
        $this->assertThat($sessionStatus->getResult(), $this->equalTo('OK'));
        $this->assertThat($sessionStatus->getState(), $this->equalTo('COMPLETE'));
        $this->assertThat($sessionStatus->getSignature()->getAlgorithmName(), $this->equalTo('SHA256WithECEncryption'));
    }

    /**
     * @test
     * @throws Exception
     */
    public function mobileAuthenticate_usingCorrectSessionStatus_getCorrectMobileIdAuthentication()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $resp = self::generateSessionId($client);

        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($resp->getSessionId());

        $hashToSign = MobileIdAuthenticationHashToSign::newBuilder()
                ->withHashType(HashType::SHA256)
                ->withHashInBase64(TestData::SHA256_HASH_IN_BASE64)
                ->build();

        $authentication = $client->createMobileIdAuthentication($sessionStatus, $hashToSign);
        $this->assertEquals(true, !is_null($authentication));
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

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function mobileAuthenticate_relyingPartyNameEmpty_shouldThrowParameterMissingException()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName("")
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $resp = self::generateSessionId($client);
    }

    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function mobileAuthenticate_noRelyingPartyUUID_shouldThrowParameterMissingException()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $resp = self::generateSessionId($client);
    }


    /**
     * @test
     * @expectedException ParameterMissingException
     */
    public function mobileAuthenticate_relyingPartyUUID_shouldThrowParameterMissingException()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID("")
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        $resp = self::generateSessionId($client);
    }

    private static function generateSessionId(MobileIdClient $client) : AuthenticationResponse
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