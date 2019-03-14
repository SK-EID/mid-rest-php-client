<?php
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

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/20/2019
 * Time: 4:38 PM
 */
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
            ->withHashToSign(MobileIdRestServiceRequestDummy::calculateMobileIdAuthenticationHash())
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
            TestCase::assertEquals(HashType::SHA512, $request->getHashType());
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
