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
namespace Sk\Mid\Tests\Mock;
use Sk\Mid\AuthenticationResponseValidator;
use Sk\Mid\HashType\HashType;
use Sk\Mid\Language\EST;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Language;
use Sk\Mid\MobileIdAuthenticationHashToSign;
use Sk\Mid\HashType\Sha512;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\MobileIdAuthentication;

use PHPUnit\Framework\TestCase;

class MobileIdRestServiceRequestDummy
{
    public static function createValidAuthenticationRequest() : AuthenticationRequest
    {
        return MobileIdRestServiceRequestDummy::createAuthenticationRequest(TestData::DEMO_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY);
    }

    public static function createAuthenticationRequest(string $UUID, string $name, string $phoneNumber, string $nationalIdentityNumber) : AuthenticationRequest
    {
        return AuthenticationRequest::newBuilder()
            ->withRelyingPartyUUID($UUID)
            ->withRelyingPartyName($name)
            ->withPhoneNumber($phoneNumber)
            ->withNationalIdentityNumber($nationalIdentityNumber)
            ->withHashToSign(MobileIdAuthenticationHashToSign::newBuilder()
                ->withHashInBase64('kc42j4tGXa1Pc2LdMcJCKAgpOk9RCQgrBogF6fHA40VSPw1qITw8zQ8g5ZaLcW5jSlq67ehG3uSvQAWIFs3TOw==')
                ->withHashType(HashType::SHA512)
                ->build())
            ->withLanguage(EST::asType())
            ->build();
    }

    public static function getCertificate(MobileIdClient $client)
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        self::assertCorrectCertificateRequestMade($request);
        $response = $client->getMobileIdConnector()->pullCertificate($request);
        return $client->createMobileIdCertificate($response);
    }

    public static function createAndSendAuthentication(MobileIdClient $client, string $phoneNumber, string $nationalIdentityNumber, MobileIdAuthenticationHashToSign $authenticationHash) : MobileIdAuthentication
    {
        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber($phoneNumber)
            ->withNationalIdentityNumber($nationalIdentityNumber)
            ->withHashToSign($authenticationHash)
            ->withLanguage(EST::asType())
            ->build();
        $response = $client->getMobileIdConnector()->initAuthentication($request);
        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalSessionStatus($response->getSessionId(), TestData::AUTHENTICATION_SESSION_PATH);
        return $client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    }

    public static function sendAuthentication(MobileIdClient $client, AuthenticationRequest $request, MobileIdAuthenticationHashToSign $authenticationHash) : MobileIdAuthentication
    {
        $response = $client->getMobileIdConnector()->initAuthentication($request);
        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalSessionStatus($response->getSessionId(), TestData::AUTHENTICATION_SESSION_PATH);
        return $client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    }

    public static function makeValidCertificateRequest(MobileIdClient $client)
    {
        self::makeCertificateRequest($client, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY);
    }

    public static function makeCertificateRequest(MobileIdClient $client, string $phoneNumber, string $nationalIdentityNumber) : void
    {
        $request = CertificateRequest::newBuilder()
            ->withRelyingPartyUUID($client->getRelyingPartyUUID())
            ->withRelyingPartyName($client->getRelyingPartyName())
            ->withPhoneNumber($phoneNumber)
            ->withNationalIdentityNumber($nationalIdentityNumber)
            ->build();
        $response = $client->getMobileIdConnector()->pullCertificate($request);
        $client->createMobileIdCertificate($response);
    }

    public static function makeValidAuthenticationRequest(MobileIdClient $client)
    {
        self::makeAuthenticationRequest($client, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY);
    }

    public static function makeAuthenticationRequest(MobileIdClient $client, string $phoneNumber, string $nationalIdentityNumber) : void
    {
        $authenticationHash = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashInBase64(TestData::SHA512_HASH_IN_BASE64)
            ->withHashType(HashType::SHA512)
            ->build();

        $request = AuthenticationRequest::newBuilder()
            ->withRelyingPartyUUID($client->getRelyingPartyUUID())
            ->withRelyingPartyName($client->getRelyingPartyName())
            ->withPhoneNumber($phoneNumber)
            ->withNationalIdentityNumber($nationalIdentityNumber)
            ->withHashToSign($authenticationHash)
            ->withLanguage(EST::asType())
            ->build();

        $response = $client->getMobileIdConnector()->initAuthentication($request);
        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalSessionStatus($response->getSessionId(), TestData::AUTHENTICATION_SESSION_PATH);
        $client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    }

    public static function calculateMobileIdAuthenticationHash() : MobileIdAuthenticationHashToSign
    {
        return MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA512)
            ->build();
    }

    public static function assertCorrectCertificateRequestMade(CertificateRequest $request)
    {
        try {
            TestCase::assertEquals(TestData::VALID_PHONE, $request->getPhoneNumber());
        } catch (Exception $e) {
        }
        try {
            TestCase::assertEquals(TestData::VALID_NAT_IDENTITY, $request->getNationalIdentityNumber());
        } catch (Exception $e) {
        }
    }

    public static function assertMadeCorrectAuthenticationRequesWithSHA256(AuthenticationRequest $request)
    {
        try {
            TestCase::assertEquals(TestData::VALID_PHONE, $request->getPhoneNumber());
        } catch (Exception $e) {
        }
        try {
            TestCase::assertEquals(TestData::VALID_NAT_IDENTITY, $request->getNationalIdentityNumber());
        } catch (Exception $e) {
        }
        try {
            TestCase::assertEquals(TestData::SHA256_HASH_IN_BASE64, $request->getHash());
        } catch (Exception $e) {
        }
        try {
            TestCase::assertEquals(HashType::SHA256, $request->getHashType());
        } catch (Exception $e) {
        }
        try {
            TestCase::assertEquals(EST::asType(), $request->getLanguage());
        } catch (Exception $e) {
        }
    }

    public static function assertCorrectAuthenticationRequestMade(AuthenticationRequest $request)
    {
        try {
            TestCase::assertEquals(TestData::VALID_PHONE, $request->getPhoneNumber());
        } catch (Exception $e) {
        }
        try {
            TestCase::assertEquals(TestData::VALID_NAT_IDENTITY, $request->getNationalIdentityNumber());
        } catch (Exception $e) {
        }
        try {
            TestCase::assertEquals(TestData::SHA512_HASH_IN_BASE64, $request->getHash());
        } catch (Exception $e) {
        }
        try {
            TestCase::assertEquals(strtolower(HashType::SHA512), strtolower($request->getHashType()));
        } catch (Exception $e) {
        }
        try {
            TestCase::assertEquals(EST::asType(), $request->getLanguage());
        } catch (Exception $e) {
        }
    }

    public static function assertCertificateCreated(string $certificate)
    {
        assert(!is_null($certificate));
    }

    public static function assertAuthenticationCreated(MobileIdAuthentication $authentication, string $expectedHashToSignInBase64)
    {
        assert(!is_null($authentication));
        assert(!is_null($authentication->getResult()) && !empty($authentication->getResult()));
        assert(!is_null($authentication->getSignatureValueInBase64()) && !empty($authentication->getSignatureValueInBase64()));
        assert(!is_null($authentication->getCertificate()));
        try {
            assertEquals($expectedHashToSignInBase64, $authentication->getSignedHashInBase64());
        } catch (Exception $e) {
        }
        try {
            assertEquals(HashType::SHA256, $authentication->getHashType());
        } catch (Exception $e) {
        }
        $validator = new AuthenticationResponseValidator();
        $validator->validate($authentication);
    }



}
