<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2021 SK ID Solutions AS
 * %%
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * #L%
 */
namespace Sk\Mid\Tests\integration;
use PHPUnit\Framework\TestCase;
use Sk\Mid\Exception\MidDeliveryException;
use Sk\Mid\Exception\MidInvalidUserConfigurationException;
use Sk\Mid\Exception\MidNotMidClientException;
use Sk\Mid\Exception\MidPhoneNotAvailableException;
use Sk\Mid\Exception\MidSessionTimeoutException;
use Sk\Mid\Exception\MidUserCancellationException;
use Sk\Mid\HashType\HashType;
use Sk\Mid\Language\ENG;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Rest\Dao\Response\AuthenticationResponse;
use Sk\Mid\Rest\MobileIdRestConnector;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
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
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $authenticationRequest = AuthenticationRequest::newBuilder()
                ->withNationalIdentityNumber(60001019906)
                ->withPhoneNumber("+37200000766")
                ->withLanguage(ENG::asType())
                ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
                ->build();

        $authResponse = $client->getMobileIdConnector()->initAuthentication($authenticationRequest);

        $this->assertEquals(36, strlen($authResponse->getSessionId()));
    }

    /**
     * @test
     */
    public function mobileAuthenticateTest_notMidClient()
    {
        $this->expectException(MidNotMidClientException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $authenticationRequest = AuthenticationRequest::newBuilder()
                ->withNationalIdentityNumber(50001018832)
                ->withPhoneNumber("+37060000266")
                ->withLanguage(ENG::asType())
                ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
                ->build();

        $authResponse = $client->getMobileIdConnector()->initAuthentication($authenticationRequest);

        $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($authResponse->getSessionId());
    }

    /**
     * @test
     */
    public function mobileAuthenticateTest_deliveryError()
    {
        $this->expectException(MidDeliveryException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $authenticationRequest = AuthenticationRequest::newBuilder()
                ->withNationalIdentityNumber(60001019947)
                ->withPhoneNumber("+37207110066")
                ->withLanguage(ENG::asType())
                ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
                ->build();

        $authResponse = $client->getMobileIdConnector()->initAuthentication($authenticationRequest);

        $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($authResponse->getSessionId());
    }

    /**
     * @test
     */
    public function mobileAuthenticateTest_userCancelled()
    {
        $this->expectException(MidUserCancellationException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $authenticationRequest = AuthenticationRequest::newBuilder()
                ->withNationalIdentityNumber(50001018854)
                ->withPhoneNumber("+37061100266")
                ->withLanguage(ENG::asType())
                ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
                ->build();

        $authResponse = $client->getMobileIdConnector()->initAuthentication($authenticationRequest);

        $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($authResponse->getSessionId());
    }

    /**
     * @test
     */
    public function mobileAuthenticateTest_signatureHashMismatch()
    {
        $this->expectException(MidInvalidUserConfigurationException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $authenticationRequest = AuthenticationRequest::newBuilder()
                ->withNationalIdentityNumber(60001019961)
                ->withPhoneNumber("+37200000666")
                ->withLanguage(ENG::asType())
                ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
                ->build();

        $authResponse = $client->getMobileIdConnector()->initAuthentication($authenticationRequest);

        $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($authResponse->getSessionId());
    }

    /**
     * @test
     */
    public function mobileAuthenticateTest_simError()
    {
        $this->expectException(MidDeliveryException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $authenticationRequest = AuthenticationRequest::newBuilder()
                ->withNationalIdentityNumber(50001018876)
                ->withPhoneNumber("+37061200266")
                ->withLanguage(ENG::asType())
                ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
                ->build();

        $authResponse = $client->getMobileIdConnector()->initAuthentication($authenticationRequest);

        $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($authResponse->getSessionId());
    }

    /**
     * @test
     */
    public function mobileAuthenticateTest_phoneAbsent()
    {
        $this->expectException(MidPhoneNotAvailableException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $authenticationRequest = AuthenticationRequest::newBuilder()
                ->withNationalIdentityNumber(60001019983)
                ->withPhoneNumber("+37213100266")
                ->withLanguage(ENG::asType())
                ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
                ->build();

        $authResponse = $client->getMobileIdConnector()->initAuthentication($authenticationRequest);

        $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($authResponse->getSessionId());
    }

    /**
     * @test
     */
    public function mobileAuthenticateTest_timeout()
    {
        $this->expectException(MidSessionTimeoutException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $authenticationRequest = AuthenticationRequest::newBuilder()
                ->withNationalIdentityNumber(50001018908)
                ->withPhoneNumber("+37066000266")
                ->withLanguage(ENG::asType())
                ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
                ->build();

        $authResponse = $client->getMobileIdConnector()->initAuthentication($authenticationRequest);

        $client->getSessionStatusPoller()->fetchFinalAuthenticationSession($authResponse->getSessionId());
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
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
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
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
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
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
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
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
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
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
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
            ->withMobileIdConnector(MobileIdRestConnector::newBuilder()
                ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
                ->build())
            ->build();

        self::generateSessionId($client);
    }

    private static function generateSessionId(MobileIdClient $client) : AuthenticationResponse
    {
        $authenticationRequest = AuthenticationRequest::newBuilder()
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withLanguage(ENG::asType())
            ->withHashToSign(MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType())
            ->build();

        $resp = $client->getMobileIdConnector()->initAuthentication($authenticationRequest);
        return $resp;
    }

}
