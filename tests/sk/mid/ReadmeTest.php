<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/sk/mid/AuthenticationResponseValidator.php';
require_once __DIR__ . '/../../../src/sk/mid/Language.php';
require_once __DIR__ . '/../../../src/sk/mid/DisplayTextFormat.php';
require_once __DIR__ . '/../../../src/sk/mid/MobileIdClient.php';
require_once __DIR__ . '/../../../src/sk/mid/exception/UnauthorizedException.php';
require_once __DIR__ . '/../../../src/sk/mid/rest/dao/request/AuthenticationRequest.php';
require_once __DIR__ . '/../../../src/sk/mid/rest/dao/request/CertificateRequest.php';
require_once __DIR__ . '/../../../src/sk/mid/MobileIdAuthenticationHashToSign.php';
require_once __DIR__ . '/../../../src/sk/mid/MobileIdAuthenticationResult.php';


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
            ->withHostUrl("https://tsp.demo.sk.ee/mid-api")
            ->withRelyingPartyUUID("00000000-0000-0000-0000-000000000000")
            ->withRelyingPartyName("DEMO")
            ->build();

        $sessionStatus = new SessionStatus();
        $authenticationHash = MobileIdAuthenticationHashToSign::newBuilder()
            ->withHashType(HashType::SHA512)
            ->build();

        $this->authentication = MobileIdAuthentication::newBuilder()->build();
        $this->authenticationResult = new MobileIdAuthenticationResult();
    }

    private function getClient() : MobileIdClient {
        return $this->client;
    }

    private function getAuthenticationResult() : MobileIdAuthenticationResult {
        return $this->authenticationResult;
    }

    private function getAuthentication() : AuthenticationResponse {
        return $this->authentication;
    }

    /**
     * @test
     * @throws Exception
     */
    public function documentConfigureTheClient()
    {
        $client = MobileIdClient::newBuilder()
            ->withHostUrl("https://tsp->demo->sk->ee")
            ->withRelyingPartyUUID("00000000-0000-0000-0000-000000000000")
            ->withRelyingPartyName("DEMO")
            ->build();

        $this->assertNotNull($client);
    }

    /**
     * @test
     * @throws Exception
     */
    public function documentClientWithPollingTimeout()
    {
        $client = MobileIdClient::newBuilder()
            ->withPollingSleepTimeoutSeconds(60)
            ->build();

        $this->assertNotNull($client);
    }

    /**
     * @test
     * @expectedException NotMidClientException
     */
    public function documentRetrieveCert()
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber("+37060000666")
            ->withNationalIdentityNumber("60001019906")
            ->build();

        $response = $this->getClient()->getMobileIdConnector()->getCertificate($request);

        $certificate = $this->getClient()->createMobileIdCertificate($response);
    }


    /**
     * @test
     * @throws Exception
     */
    public function documentGetAuthenticationResponse()
    {
        $authenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();

        $verificationCode = $authenticationHash->calculateVerificationCode();

        $request = AuthenticationRequest::newBuilder()
            ->withPhoneNumber("+37200000766")
            ->withNationalIdentityNumber("60001019906")
            ->withHashToSign($authenticationHash)
            ->withLanguage(ENG::asType())
            ->withDisplayText("Log into self-service?")
            ->withDisplayTextFormat(DisplayTextFormat::GSM7)
            ->build();

        $response = $this->getClient()->getMobileIdConnector()->authenticate($request);

        $sessionStatus = $this->getClient()->getSessionStatusPoller()->fetchFinalSessionStatus($response->getSessionID());

        $authentication = $this->getClient()->createMobileIdAuthentication($sessionStatus, $authenticationHash);
        $this->assertEquals(true, !is_null($authentication));
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
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
     * @expectedException UnauthorizedException
     */
    public function documentAuthenticationIdentityUsage()
    {
        $authenticationIdentity = $this->getAuthenticationResult()->getAuthenticationIdentity();
        if ($authenticationIdentity == null) throw new UnauthorizedException();
        $givenName = $authenticationIdentity->getGivenName();
        $surName = $authenticationIdentity->getSurName();
        $identityCode = $authenticationIdentity->getIdentityCode();
        $country = $authenticationIdentity->getCountry();
    }


}
