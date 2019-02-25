<?php

use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/22/2019
 * Time: 8:20 AM
 */
class ReadmeTest extends TestCase
{
    private $client;

    private $authentication;

    private $authenticationResult;

    protected function setUp()
    {
        $this->client = MobileIdClient::newBuilder()
            ->withHostUrl("https://tsp->demo->sk->ee")
            ->withRelyingPartyUUID("00000000-0000-0000-0000-000000000000")
            ->withRelyingPartyName("DEMO")
            ->build();

        $sessionStatus = new SessionStatus();
        $authenticationHash = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA512)
            ->withHashInBase64("XXX")
            ->build();

        $authentication = MobileIdAuthentication::newBuilder()->build();
        $authenticationResult = new MobileIdAuthenticationResult();
    }

    /**
     * @test
     */
    public function documentConfigureTheClient()
    {
        $client = MobileIdClient::newBuilder()
            ->withHostUrl("https://tsp->demo->sk->ee")
            ->withRelyingPartyUUID("00000000-0000-0000-0000-000000000000")
            ->withRelyingPartyName("DEMO")
            ->build();
    }

    /**
     * @test
     */
    public function documentClientWithPollingTimeout()
    {
        $client = MobileIdClient::newBuilder()
            ->withPollingSleepTimeoutSeconds(60)
            ->build();
    }

    /**
     * @test
     */
    public function documentRetrieveCert()
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber("+37060000666")
            ->withNationalIdentityNumber("60001019906")
            ->build();

        $response = $this->client->getMobileIdConnector()->getCertificate($request);

        $certificate = $this->client->createMobileIdCertificate($response);
    }

    /**
     * @test
     */
    public function documentCreateSignatureFromExistingHash()
    {
        $hashToSign = HashToSign::newBuilder()
            ->withHashInBase64("AE7S1QxYjqtVv+Tgukv2bMMi9gDCbc9ca2vy/iIG6ug=")
            ->withHashType(HashType::SHA256)
            ->build();


        $verificationCode = $hashToSign->calculateVerificationCode();

        $request = SignatureRequest::newBuilder()
            ->withPhoneNumber("+37200000766")
            ->withNationalIdentityNumber("60001019906")
            ->withHashToSign($hashToSign)
            ->withLanguage(Language::ENG)
            ->build();

        $response = $this->client->getMobileIdConnector()->sign($request);

        $sessionStatus = $this->client->getSessionStatusPoller()->fetchFinalSessionStatus(
            $response->getSessionID(),
            "/mid-api/signature/session/{sessionId}"
        );

        $signature = $this->client->createMobileIdSignature($sessionStatus);
    }

    /**
     * @test
     */
    public function documentGetAuthenticationResponse()
    {
        $authenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();

        $verificationCode = $authenticationHash->calculateVerificationCode();

        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber("+37200000766")
            ->withNationalIdentityNumber("60001019906")
            ->withHashToSign($authenticationHash)
            ->withLanguage(Language::ENG)
            ->withDisplayText("Log into self-service?")
            ->withDisplayTextFormat(DisplayTextFormat::GSM7)
            ->build();

        $response = $this->client->getMobileIdConnector()->authenticate($request);

        $sessionStatus = $this->client->getSessionStatusPoller()->fetchFinalSessionStatus($response->getSessionID(),
            "/mid-api/authentication/session/{sessionId}");

        $authentication = $this->client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     * @throws Exception
     */
    public function documentHowToVerifyAuthenticationResult()
    {
        $validator = new AuthenticationResponseValidator();
        $authenticationResult = $validator->validate($this->authentication);

        $this->assertEquals(true, $authenticationResult->isValid());
        $this->assertEquals(true, count($authenticationResult->getErrors()) == 0);

    }

    /**
     * @test
     */
    public function documentGettingErrors()
    {
        $errors = $this->authenticationResult->getErrors();
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function documentAuthenticationIdentityUsage()
    {
        $authenticationIdentity = $this->authenticationResult->getAuthenticationIdentity();
        $givenName = $authenticationIdentity->getGivenName();
        $surName = $authenticationIdentity->getSurName();
        $identityCode = $authenticationIdentity->getIdentityCode();
        $country = $authenticationIdentity->getCountry();
    }


}