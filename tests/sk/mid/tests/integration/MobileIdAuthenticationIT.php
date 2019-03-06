<?php
namespace sk\mid\tests\integration;

use Exception;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\TestCase;
use sk\mid\ENG;
use sk\mid\exception\MissingOrInvalidParameterException;
use sk\mid\HashType;
use sk\mid\MobileIdClient;
use sk\mid\rest\dao\response\AuthenticationResponse;
use sk\mid\rest\dao\SessionStatus;
use sk\mid\rest\MobileIdRestConnector;
use sk\mid\rest\SessionStatusPoller;
use sk\mid\util\DigestCalculator;
use sk\mid\util\Logger;
use sk\mid\tests\mock\TestData;
use sk\mid\rest\dao\request\AuthenticationRequest;
use sk\mid\Language;
use sk\mid\MobileIdAuthenticationHashToSign;
use sk\mid\exception\NotMidClientException;

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
        $this->assertThat($sessionStatus->getSignature()->getValueInBase64(), $this->equalTo(''));
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
     * @expectedException MissingOrInvalidParameterException
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
     * @expectedException MissingOrInvalidParameterException
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
     * @expectedException MissingOrInvalidParameterException
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
     * @expectedException MissingOrInvalidParameterException
     */
    public function mobileAuthenticate_relyingPartyUUIDEmpty_shouldThrowParameterMissingException()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID("")
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withNetworkConnectionConfig("")
            ->withMobileIdConnector(MobileIdRestConnector::newBuilder()->build())
            ->build();

        $resp = self::generateSessionId($client);
    }

    private static function generateSessionId(MobileIdClient $client) : AuthenticationResponse
    {
        $authenticationRequest = AuthenticationRequest::newBuilder()
            ->withNationalIdentityNumber(60001019906)
            ->withPhoneNumber("+37200000766")
            ->withLanguage(ENG::asType())
            ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
            ->build();

        $resp = $client->getMobileIdConnector()->authenticate($authenticationRequest);
        return $resp;
    }

}
