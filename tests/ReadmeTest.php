<?php
namespace Sk\Mid\Tests;
use PHPUnit\Framework\TestCase;
use Sk\Mid\Exception\MidServiceUnavailableException;
use Sk\Mid\Exception\MidSslException;
use Sk\Mid\AuthenticationResponseValidator;
use Sk\Mid\Exception\MidDeliveryException;
use Sk\Mid\Exception\MidInvalidNationalIdentityNumberException;
use Sk\Mid\Exception\MidInvalidPhoneNumberException;
use Sk\Mid\Exception\MidInvalidUserConfigurationException;
use Sk\Mid\Exception\MidSessionNotFoundException;
use Sk\Mid\Exception\MidSessionTimeoutException;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\Exception\MidPhoneNotAvailableException;
use Sk\Mid\Exception\MidUserCancellationException;
use Sk\Mid\Language\ENG;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Exception\MidNotMidClientException;
use Sk\Mid\DisplayTextFormat;
use Sk\Mid\MobileIdAuthentication;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Exception\MidUnauthorizedException;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\MobileIdAuthenticationHashToSign;
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

    private $userData;

    private $config;

    protected function setUp() : void
    {

        $this->userData = [
            'phoneNumber' => '+37200000766',
            'nationalIdentityNumber' => '60001019906',
        ];
        $this->config = [
            'relyingPartyUUID' => '00000000-0000-0000-0000-000000000000',
            'relyingPartyName' => 'DEMO',
            'hostUrl' => 'https://tsp.demo.sk.ee/mid-api',
        ];

    }

    /**
     * @test
     */
    public function documentAuthenticationProcess()
    {
        // See (ReadmeTest.php)[blob/master/tests/ReadmeTest.php] for list of classes to 'use'

        // step #1 - validate user input

        try {
            $phoneNumber = MidInputUtil::getValidatedPhoneNumber($this->userData['phoneNumber']);
            $nationalIdentityNumber = MidInputUtil::getValidatedNationalIdentityNumber($this->userData['nationalIdentityNumber']);
        }
        catch (MidInvalidPhoneNumberException $e) {
            die('The phone number you entered is invalid');
        }
        catch (MidInvalidNationalIdentityNumberException $e) {
            die('The national identity number you entered is invalid');
        }

        // step #2 - create client with long-polling.
        // withSslPinnedPublicKeys() is explained later in this document

        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID($this->config['relyingPartyUUID'])
                ->withRelyingPartyName($this->config['relyingPartyName'])
                ->withHostUrl($this->config['hostUrl'])
                ->withLongPollingTimeoutSeconds(60)
                ->withSslPinnedPublicKeys("sha256//k/w7/9MIvdN6O/rE1ON+HjbGx9PRh/zSnNJ61pldpCs=;sha256//some-future-ssl-host-key")
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
        catch (MidNotMidClientException $e) {
            die("User is not a MID client or user's certificates are revoked.");
        }
        catch (MidUnauthorizedException $e) {
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

        // step #8 - get authenticationResult

        try {
            $authenticationResult = $client
                ->createMobileIdAuthentication($finalSessionStatus, $authenticationHash);

        }
        catch (MidUserCancellationException $e) {
            die("User cancelled operation from his/her phone.");
        }
        catch (MidNotMidClientException $e) {
            die("User is not a MID client or user's certificates are revoked.");
        }
        catch (MidSessionTimeoutException $e) {
            die("User did not type in PIN code or communication error.");
        }
        catch (MidPhoneNotAvailableException $e) {
            die("Unable to reach phone/SIM card. User needs to check if phone has coverage.");
        }
        catch (MidDeliveryException $e) {
            die("Error communicating with the phone/SIM card.");
        }
        catch (MidInvalidUserConfigurationException $e) {
            die("Mobile-ID configuration on user's SIM card differs from what is configured on service provider's side. User needs to contact his/her mobile operator.");
        }
        catch (MidSessionNotFoundException | MissingOrInvalidParameterException | MidUnauthorizedException | MidSslException $e) {
            die("Integrator-side error with MID integration or configuration. Error code:". $e->getCode());
        }
        catch (MidServiceUnavailableException $e) {
            die("MID service is currently unavailable. User shold try again later.");
        }
        catch (MidInternalErrorException $internalError) {
            die("Something went wrong with Mobile-ID service");
        }

        # step #9 - validate returned result (to protect yourself from man-in-the-middle attack)
        $validator = AuthenticationResponseValidator::newBuilder()
            ->withTrustedCaCertificatesFolder(__DIR__ . "/test_numbers_ca_certificates/")
            ->build();

        $validator->validate($authenticationResult);


        # step #10 - read out authenticated person details

        $authenticatedPerson = $authenticationResult->constructAuthenticationIdentity();

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
                ->withRelyingPartyUUID($this->config['relyingPartyUUID'])
                ->withRelyingPartyName($this->config['relyingPartyName'])
                ->withHostUrl($this->config['hostUrl'])
                ->withSslPinnedPublicKeys("sha256//k/w7/9MIvdN6O/rE1ON+HjbGx9PRh/zSnNJ61pldpCs=;sha256//some-future-ssl-host-key")
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
        catch (MidNotMidClientException $e) {
            // if user is not MID client then this exception is thrown and caught already during first request (see above)
            die("You are not a Mobile-ID client or your Mobile-ID certificates are revoked. Please contact your mobile operator.");
        }
        catch (MissingOrInvalidParameterException | MidUnauthorizedException $e) {
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
    public function documentLongPolling()
    {
        $this->client = MobileIdClient::newBuilder()
            ->withHostUrl("https://...")
            ->withRelyingPartyUUID("...")
            ->withRelyingPartyName("...")
            ->withSslPinnedPublicKeys("sha256//...")
            ->withLongPollingTimeoutSeconds(60)
            ->build();

        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function documentWithoutLongPolling()
    {
        $this->client = MobileIdClient::newBuilder()
            ->withHostUrl("https://...")
            ->withRelyingPartyUUID("...")
            ->withRelyingPartyName("...")
            ->withSslPinnedPublicKeys("sha256//...")
            ->withPollingSleepTimeoutSeconds(2)
            ->build();

        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function documentPinning()
    {
        $this->client = MobileIdClient::newBuilder()
            ->withHostUrl("https://...")
            ->withRelyingPartyUUID("...")
            ->withRelyingPartyName("...")
            ->withSslPinnedPublicKeys("sha256//hash-of-current-mid-api-ssl-host-public-key;sha256//hash-of-future-mid-api-ssl-host-public-key")
            ->build();


        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function documentValidation()
    {
        $this->expectException(MidInternalErrorException::class);

        $validator = AuthenticationResponseValidator::newBuilder()
            ->withTrustedCaCertificatesFolder(__DIR__ . "/test_numbers_ca_certificates/")
            ->build();

        $authentication = MobileIdAuthentication::newBuilder()->build();

        $authenticationResult = $validator->validate($authentication);

        $this->assertEquals(true, $authenticationResult->isValid());
        $this->assertEquals(true, count($authenticationResult->getErrors()) == 0);
    }



}
