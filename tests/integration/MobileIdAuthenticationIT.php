<?php
namespace Sk\Mid\Tests\integration;
use PHPUnit\Framework\TestCase;
use Sk\Mid\HashType\HashType;
use Sk\Mid\Language\ENG;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Rest1\Dao\Response1\AuthenticationResponse;
use Sk\Mid\Rest1\MobileIdRestConnector;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Rest1\Dao\Request\AuthenticationRequest;
use Sk\Mid\MobileIdAuthenticationHashToSign;

class MobileIdAuthenticationIT extends TestCase
{

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

        $this->assertEquals(36, strlen($resp->getSessionId()));
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

        $authenticationResponse = self::generateSessionId($client);

        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($authenticationResponse->getSessionId());

        $this->assertThat($sessionStatus, $this->logicalNot($this->isNull()));
        $this->assertThat($sessionStatus->getResult(), $this->equalTo('OK'));
        $this->assertThat($sessionStatus->getState(), $this->equalTo('COMPLETE'));
        $this->assertThat($sessionStatus->getSignature()->getAlgorithmName(), $this->equalTo('SHA256WithECEncryption'));
        $this->assertEquals(true, !is_null($sessionStatus->getSignature()->getValueInBase64()));
        $this->assertEquals(true, !empty($sessionStatus->getSignature()->getValueInBase64()));
    }

    /**
     * @test
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
     */
    public function mobileAuthenticate_noRelyingPartyName_shouldThrowParameterMissingException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        self::generateSessionId($client);
    }

    /**
     * @test
     */
    public function mobileAuthenticate_relyingPartyNameEmpty_shouldThrowParameterMissingException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName("")
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        self::generateSessionId($client);
    }

    /**
     * @test
     */
    public function mobileAuthenticate_noRelyingPartyUUID_shouldThrowParameterMissingException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();

        self::generateSessionId($client);
    }


    /**
     * @test
     */
    public function mobileAuthenticate_relyingPartyUUIDEmpty_shouldThrowParameterMissingException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID("")
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withNetworkConnectionConfig("")
            ->withMobileIdConnector(MobileIdRestConnector::newBuilder()->build())
            ->build();

        self::generateSessionId($client);
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
