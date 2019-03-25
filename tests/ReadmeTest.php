<?php
namespace Sk\Mid\Tests;
use PHPUnit\Framework\TestCase;
use Sk\Mid\MidIdentity;
use Sk\Mid\AuthenticationResponseValidator;
use Sk\Mid\CertificateParser;
use Sk\Mid\Exception\DeliveryException;
use Sk\Mid\Exception\InvalidNationalIdentityNumberException;
use Sk\Mid\Exception\InvalidPhoneNumberException;
use Sk\Mid\Exception\InvalidUserConfigurationException;
use Sk\Mid\Exception\MidSessionNotFoundException;
use Sk\Mid\Exception\MidSessionTimeoutException;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\Exception\PhoneNotAvailableException;
use Sk\Mid\Exception\UserCancellationException;
use Sk\Mid\Language\ENG;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Exception\NotMidClientException;
use Sk\Mid\HashType\HashType;
use Sk\Mid\Language\Language;
use Sk\Mid\DisplayTextFormat;
use Sk\Mid\MobileIdAuthentication;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Exception\UnauthorizedException;
use Sk\Mid\Rest\Dao\MidCertificate;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\MobileIdAuthenticationHashToSign;
use Sk\Mid\MobileIdAuthenticationResult;
use Sk\Mid\Rest\Dao\Response\AuthenticationResponse;
use Sk\Mid\Rest\Dao\SessionStatus;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Util\MidInputUtil;

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

    protected function setUp() : void
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


        $_GET['phoneNumber'] = TestData::VALID_PHONE;
        $_GET['nationalIdentityNumber'] = TestData::VALID_NAT_IDENTITY;
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
     * @throws \Exception
     */
    public function documentAuthenticationProcess()
    {
        // step #1 - validate user input

        try {
            $phoneNumber = MidInputUtil::getValidatedPhoneNumber($_GET['phoneNumber']);
            $nationalIdentityNumber = MidInputUtil::getValidatedNationalIdentityNumber($_GET['nationalIdentityNumber']);
        }
        catch (InvalidPhoneNumberException $e) {
            die('The phone number you entered is invalid');
        }
        catch (InvalidNationalIdentityNumberException $e) {
            die('The national identity number you entered is invalid');
        }

        // step #2 - create client with long-polling

        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID("00000000-0000-0000-0000-000000000000")
                ->withRelyingPartyName("DEMO")
                ->withHostUrl("https://tsp.demo.sk.ee/mid-api")
                ->withLongPollingTimeoutSeconds(60)
                ->withPollingSleepTimeoutSeconds(2)
                ->build();


        // step #3 - generate hash & calculate verification code and display to user

        $authenticationHash = MobileIdAuthenticationHashToSign::generateRandomHashOfDefaultType();
        $verificationCode = $authenticationHash->calculateVerificationCode();

        // step #4 - display $verificationCode (4 digit code) to user

        echo 'Verification code: '.$verificationCode."\n";

        // step #5 - create request to be sent to user's phone

        $request = AuthenticationRequest::newBuilder()
                ->withPhoneNumber($phoneNumber)
                ->withNationalIdentityNumber($nationalIdentityNumber)
                ->withHashToSign($authenticationHash)
                ->withLanguage(ENG::asType())
                ->withDisplayText("Log into self-service?")
                ->withDisplayTextFormat(DisplayTextFormat::GSM7)
                ->build();

        // step #6 - send request to user's phone and catch possible errors

        try {
            $response = $client->getMobileIdConnector()->initAuthentication($request);
        }
        catch (NotMidClientException $e) {
            die("You are not a Mobile-ID client or your Mobile-ID certificates are revoked. Please contact your mobile operator.");
        }
        catch (UnauthorizedException $e) {
            die('Integration error with Mobile-ID. Invalid MID credentials');
        }
        catch (MissingOrInvalidParameterException $e) {
            die('Problem with MID integration');
        }
        catch (MidInternalErrorException $e) {
            die('MID internal error');
        }

        // step #7 - keep polling for session status until we have a final status from phone

        $finalSessionStatus = $client
                ->getSessionStatusPoller()
                ->fetchFinalSessionStatus($response->getSessionID());

        // step #8 - parse authenticated person out of the response and get it validated

        try {
            $authenticatedPerson = $client
                ->createMobileIdAuthentication($finalSessionStatus, $authenticationHash)
                ->getValidatedAuthenticationResult()
                ->getAuthenticationIdentity();
        }
        catch (UserCancellationException $e) {
            die("You cancelled operation from your phone.");
        }
        catch (MidSessionTimeoutException $e) {
            die("You didn't type in PIN code into your phone or there was a communication error.");
        }
        catch (PhoneNotAvailableException $e) {
            die("Unable to reach your phone. Please make sure your phone has mobile coverage.");
        }
        catch (DeliveryException $e) {
            die("Communication error. Unable to reach your phone.");
        }
        catch (InvalidUserConfigurationException $e) {
            die("Mobile-ID configuration on your SIM card differs from what is configured on service provider's side. Please contact your mobile operator.");
        }
        catch (MidSessionNotFoundException | MissingOrInvalidParameterException | UnauthorizedException $e) {
            die("Client side error with mobile-ID integration. Error code:". $e->getCode());
        }
        catch (NotMidClientException $e) {
            // if user is not MID client then this exception is thrown and caught already during first request (see above)
            die("You are not a Mobile-ID client or your Mobile-ID certificates are revoked. Please contact your mobile operator.");
        }
        catch (MidInternalErrorException $internalError) {
            die("Something went wrong with Mobile-ID service");
        }

        # step #9 - read out authenticated person details

        echo 'Welcome, '.$authenticatedPerson->getGivenName().' '.$authenticatedPerson->getSurName().' ';
        echo ' (ID code '.$authenticatedPerson->getIdentityCode().') ';
        echo 'from '. $authenticatedPerson->getCountry(). '!';

        # end of example

        $this->addToAssertionCount(1);
    }


    /**
     * @test
     */
    public function documentRetrieveSigningCert()
    {
        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID("00000000-0000-0000-0000-000000000000")
                ->withRelyingPartyName("DEMO")
                ->withHostUrl("https://tsp.demo.sk.ee/mid-api")
                ->build();

        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber("+37200000766")
            ->withNationalIdentityNumber("60001019906")
            ->build();

        try {
            $response = $client->getMobileIdConnector()->pullCertificate($request);
            $person = $client->parseMobileIdIdentity($response);

            echo 'This is a Mobile-ID user.';
            echo 'Name, '.$person->getGivenName().' '.$person->getSurName().' ';
            echo ' (ID code '.$person->getIdentityCode().') ';
            echo 'from '. $person->getCountry(). '!';
        }
        catch (NotMidClientException $e) {
            // if user is not MID client then this exception is thrown and caught already during first request (see above)
            die("You are not a Mobile-ID client or your Mobile-ID certificates are revoked. Please contact your mobile operator.");
        }
        catch (MissingOrInvalidParameterException | UnauthorizedException $e) {
            die("Client side error with mobile-ID integration. Error code:". $e->getCode());
        }
        catch (MidInternalErrorException $internalError) {
            die("Something went wrong with Mobile-ID service");
        }




        $this->addToAssertionCount(1);

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
            ->withLanguage(ENG::asType())
            ->withDisplayText("Log into self-service?")
            ->withDisplayTextFormat(DisplayTextFormat::GSM7)
            ->build();

        $response = $this->getClient()->getMobileIdConnector()->initAuthentication($request);

        $sessionStatus = $this->getClient()->getSessionStatusPoller()->fetchFinalSessionStatus($response->getSessionID());

        $authentication = $this->getClient()->createMobileIdAuthentication($sessionStatus, $authenticationHash);
        $this->assertEquals(true, !is_null($authentication));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function documentHowToVerifyAuthenticationResult()
    {
        $this->expectException(MidInternalErrorException::class);

        $validator = new AuthenticationResponseValidator();
        $authenticationResult = $validator->validate($this->authentication);

        $this->assertEquals(true, $authenticationResult->isValid());
        $this->assertEquals(true, count($authenticationResult->getErrors()) == 0);
    }

    /**
     * @test
     */
    public function documentAuthenticationIdentityUsage()
    {
        $this->expectException(UnauthorizedException::class);

        $authenticationIdentity = $this->getAuthenticationResult()->getAuthenticationIdentity();
        if ($authenticationIdentity == null) throw new UnauthorizedException();
        $givenName = $authenticationIdentity->getGivenName();
        $surName = $authenticationIdentity->getSurName();
        $identityCode = $authenticationIdentity->getIdentityCode();
        $country = $authenticationIdentity->getCountry();
    }


}
