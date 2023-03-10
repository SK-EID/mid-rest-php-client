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

namespace Sk\Mid\Tests;

use http\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use Sk\Mid\CertificateParser;
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
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Util\MidInputUtil;

class ReadmeTest extends TestCase
{

    private array $userData;

    private array $config;

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
            echo 'The phone number you entered is invalid';
        }
        catch (MidInvalidNationalIdentityNumberException $e) {
            echo 'The national identity number you entered is invalid';
        }

        // step #2 - create client with long-polling.
        // withSslPinnedPublicKeys() is explained later in this document

        $client = MobileIdClient::newBuilder()
                ->withRelyingPartyUUID($this->config['relyingPartyUUID'])
                ->withRelyingPartyName($this->config['relyingPartyName'])
                ->withHostUrl($this->config['hostUrl'])
                ->withLongPollingTimeoutSeconds(60)
                ->withSslPinnedPublicKeys("sha256//Rhm2BxU8LheLZP664D3J4yIZCjkU1EQDRJnrMRggwTU=;sha256//k/w7/9MIvdN6O/rE1ON+HjbGx9PRh/zSnNJ61pldpCs=;sha256//some-future-ssl-host-key")
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
            // add exception handling logic here
            echo "User is not a MID client or user's certificates are revoked.";
        }
        catch (MidUnauthorizedException $e) {
            // add exception handling logic here
            echo 'Integration error with Mobile-ID. Invalid MID credentials';
        }
        catch (MissingOrInvalidParameterException $e) {
            // add exception handling logic here
            echo 'Problem with MID integration';
        }
        catch (MidInternalErrorException $e) {
            // add exception handling logic here
            echo 'MID internal error:' . $e;
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
            // add exception handling logic here
            echo "User cancelled operation from his/her phone.";
        }
        catch (MidNotMidClientException $e) {
            // add exception handling logic here
            echo "User is not a MID client or user's certificates are revoked.";
        }
        catch (MidSessionTimeoutException $e) {
            // add exception handling logic here
            echo "User did not type in PIN code or communication error.";
        }
        catch (MidPhoneNotAvailableException $e) {
            // add exception handling logic here
            echo "Unable to reach phone/SIM card. User needs to check if phone has coverage.";
        }
        catch (MidDeliveryException $e) {
            // add exception handling logic here
            echo "Error communicating with the phone/SIM card.";
        }
        catch (MidInvalidUserConfigurationException $e) {
            // add exception handling logic here
            echo "Mobile-ID configuration on user's SIM card differs from what is configured on service provider's side. User needs to contact his/her mobile operator.";
        }
        catch (MidSessionNotFoundException | MissingOrInvalidParameterException | MidUnauthorizedException | MidSslException $e) {
            // add exception handling logic here
            echo "Integrator-side error with MID integration or configuration. Error code:". $e->getCode();
        }
        catch (MidServiceUnavailableException $e) {
            // add exception handling logic here
            echo "MID service is currently unavailable. User shold try again later.";
        }
        catch (MidInternalErrorException $internalError) {
            // add exception handling logic here
            echo "Something went wrong with Mobile-ID service";
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
                ->withSslPinnedPublicKeys("sha256//Rhm2BxU8LheLZP664D3J4yIZCjkU1EQDRJnrMRggwTU=;sha256//k/w7/9MIvdN6O/rE1ON+HjbGx9PRh/zSnNJ61pldpCs=;sha256//some-future-ssl-host-key")
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
            echo "You are not a Mobile-ID client or your Mobile-ID certificates are revoked. Please contact your mobile operator.";
        }
        catch (MissingOrInvalidParameterException | MidUnauthorizedException $e) {
            throw new RuntimeException("Client side error with mobile-ID integration. Error code:". $e->getCode());
        }
        catch (MidInternalErrorException $internalError) {
            echo "Something went wrong with Mobile-ID service";
        }

        $this->addToAssertionCount(1);
    }


    /**
     * @test
     */
    public function documentLongPolling()
    {
        $client = MobileIdClient::newBuilder()
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
        $client = MobileIdClient::newBuilder()
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
        $client = MobileIdClient::newBuilder()
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
    public function documentNetworkInterfaceSelection()
    {
        $client = MobileIdClient::newBuilder()
            ->withHostUrl("https://...")
            ->withRelyingPartyUUID("...")
            ->withRelyingPartyName("...")
            ->withSslPinnedPublicKeys("sha256//hash-of-current-mid-api-ssl-host-public-key;sha256//hash-of-future-mid-api-ssl-host-public-key")
            ->withNetworkInterface("10.11.12.13")
            ->build();

        // end of example
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

        $authentication = MobileIdAuthentication::newBuilder()
            ->withCertificate(CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_EE))
            ->build();

        $authenticationResult = $validator->validate($authentication);

        $this->assertEquals(true, $authenticationResult->isValid());
        $this->assertEquals(true, count($authenticationResult->getErrors()) == 0);
    }



}
