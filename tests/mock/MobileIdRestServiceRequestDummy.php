<?php

require_once __DIR__ . '/../../ee.sk.mid/rest/dao/request/AuthenticationRequest.php';
require_once __DIR__ . '/../../ee.sk.mid/rest/dao/request/CertificateRequest.php';
require_once __DIR__ . '/../../ee.sk.mid/HashType.php';
require_once __DIR__ . '/../../ee.sk.mid/MobileIdAuthenticationHashToSign.php';
require_once __DIR__ . '/TestData.php';



/*
require_once __DIR__ . '/mock/MobileIdConnectorSpy.php';

require_once __DIR__ . '/../../ee.sk.mid/MobileIdClient.php';
require_once __DIR__ . '/../ee.sk.mid/rest/MobileIdRestConnector.php';

require_once __DIR__ . '/../ee.sk.mid/rest/dao/response/CertificateChoiceResponse.php';

require_once __DIR__ . '/../ee.sk.mid/exception/CertificateNotPresentException.php';
require_once __DIR__ . '/../ee.sk.mid/exception/ExpiredException.php';
require_once __DIR__ . '/../ee.sk.mid/exception/ParameterMissingException.php';
require_once __DIR__ . '/../ee.sk.mid/exception/TechnicalErrorException.php';
*/
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/20/2019
 * Time: 4:38 PM
 */
class MobileIdRestServiceRequestDummy
{
    public static function createValidAuthenticationRequest()
    {
        return MobileIdRestServiceRequestDummy::createAuthenticationRequest(TestData::DEMO_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, null, null);
    }

    public static function createAuthenticationRequest($UUID, $name, $phoneNumber, $nationalIdentityNumber)
    {
        return AuthenticationRequest::newBuilder()
            ->withRelyingPartyUUID($UUID)
            ->withRelyingPartyName($name)
            ->withPhoneNumber($phoneNumber)
            ->withNationalIdentityNumber($nationalIdentityNumber)
            ->withHashToSign(self::calculateMobileIdAuthenticationHash())
            ->withLanguage(Language::EST)
            ->build();
    }

    public static function getCertificate($client)
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        self::assertCorrectCertificateRequestMade($request);
        $response = $client->getMobileIdConnector()->getCertificate($request);
        return $client->createMobileIdCertificate($response);
    }

    public static function createAndSendAuthentication($client, $phoneNumber, $nationalIdentityNumber, $authenticationHash)
    {
        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber($phoneNumber)
            ->withNationalIdentityNumber($nationalIdentityNumber)
            ->withHashToSign($authenticationHash)
            ->withLanguage(Language::EST)
            ->build();
        $response = $client->getMobileIdConnector()->authenticate($request);
        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalSessionStatus($response->getSessionId(), TestData::AUTHENTICATION_SESSION_PATH);
        return $client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    }

    public static function sendAuthentication($client, $request, $authenticationHash)
    {
        $response = $client->getMobileIdConnector()->authenticate($request);
        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalSessionStatus($response->getSessionId(), TestData::AUTHENTICATION_SESSION_PATH);
        return $client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    }

    public static function makeValidCertificateRequest($client)
    {
        self::makeCertificateRequest($client, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY);
    }

    public static function makeCertificateRequest($client, $phoneNumber, $nationalIdentityNumber)
    {
        $request = CertificateRequest::newBuilder()
            ->withRelyingPartyUUID($client->getRelyingPartyUUID())
            ->withRelyingPartyName($client->getRelyingPartyName())
            ->withPhoneNumber($phoneNumber)
            ->withNationalIdentityNumber($nationalIdentityNumber)
            ->build();
        $response = $client->getMobileIdConnector()->getCertificate($request);
        $client->createMobileIdCertificate($response);
    }

    public static function makeValidAuthenticationRequest($client)
    {
        self::makeAuthenticationRequest($client, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY);
    }

    public static function makeAuthenticationRequest($client, $phoneNumber, $nationalIdentityNumber)
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
            ->withLanguage(Language::EST)
            ->build();

        $response = $client->getMobileIdConnector()->authenticate($request);
        $sessionStatus = $client->getSessionStatusPoller()->fetchFinalSessionStatus($response->getSessionId(), TestData::AUTHENTICATION_SESSION_PATH);
        $client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    }

    private static function calculateHashInBase64($hashType)
    {
        $digestValue = $hashType->calculateDigest(TestData::DATA_TO_SIGN);
        return base64_encode($digestValue);
    }

    private static function calculateMobileIdAuthenticationHash()
    {
        $digestValue = new Sha512();
        $digestValue->calculateDigest(TestData::DATA_TO_SIGN);
        return MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashInBase64(base64_encode($digestValue))
            ->withHashType(HashType::SHA512)
            ->build();
    }

    public static function assertCorrectCertificateRequestMade($request)
    {
        try {
            assertEquals(TestData::VALID_PHONE, $request->getPhoneNumber());
        } catch (Exception $e) {
        }
        try {
            assertEquals(TestData::VALID_NAT_IDENTITY, $request->getNationalIdentityNumber());
        } catch (Exception $e) {
        }
    }

    public static function assertMadeCorrectAuthenticationRequesWithSHA256($request)
    {
        try {
            assertEquals(TestData::VALID_PHONE, $request->getPhoneNumber());
        } catch (Exception $e) {
        }
        try {
            assertEquals(TestData::VALID_NAT_IDENTITY, $request->getNationalIdentityNumber());
        } catch (Exception $e) {
        }
        try {
            assertEquals(TestData::SHA256_HASH_IN_BASE64, $request->getHash());
        } catch (Exception $e) {
        }
        try {
            assertEquals(HashType::SHA256, $request->getHashType());
        } catch (Exception $e) {
        }
        try {
            assertEquals(Language::EST, $request->getLanguage());
        } catch (Exception $e) {
        }
    }

    public static function assertCorrectAuthenticationRequestMade($request)
    {
        try {
            assertEquals(TestData::VALID_PHONE, $request->getPhoneNumber());
        } catch (Exception $e) {
        }
        try {
            assertEquals(TestData::VALID_NAT_IDENTITY, $request->getNationalIdentityNumber());
        } catch (Exception $e) {
        }
        try {
            assertEquals(TestData::SHA512_HASH_IN_BASE64, $request->getHash());
        } catch (Exception $e) {
        }
        try {
            assertEquals(HashType::SHA512, $request->getHashType());
        } catch (Exception $e) {
        }
        try {
            assertEquals(Language::EST, $request->getLanguage());
        } catch (Exception $e) {
        }
    }

    public static function assertCertificateCreated($certificate)
    {
        assert(!isNull($certificate));
    }

    public static function assertAuthenticationCreated($authentication, $expectedHashToSignInBase64)
    {
        assert(!is_null($authentication));
        assert(!isNull($authentication->getResult()) && !empty($authentication->getResult()));
        assert(!isNull($authentication->getSignatureValueInBase64()) && !empty($authentication->getSignatureValueInBase64()));
        assert(!isNull($authentication->getCertificate()));
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