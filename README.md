# Mobile-ID (MID) PHP Rest Client
[![Tests](https://github.com/SK-EID/mid-rest-php-client/actions/workflows/test.yaml/badge.svg)](https://github.com/SK-EID/mid-rest-php-client/actions/workflows/test.yaml)
[![Coverage Status](https://img.shields.io/codecov/c/github/SK-EID/mid-rest-php-client.svg)](https://codecov.io/gh/SK-EID/mid-rest-php-client)
[![License: MIT](https://img.shields.io/github/license/mashape/apistatus.svg)](https://opensource.org/licenses/MIT)

> [!WARNING]
> **Security Notice!** Critical vulnerabilities CVE-2026-109618 and CVE-2026-109619 have been identified in PHP and may allow unauthenticated remote code execution in applications serving web requests. These vulnerabilities are in the PHP runtime and not in the Mobile-ID PHP client library itself.
>
> Integrators using this library should ensure that their applications run on a patched and supported PHP version. As of October 21, 2026, the vulnerabilities are fixed in PHP 8.2.35, 8.3.36, 8.4.27 and 8.5.12. PHP 8.1 and earlier are no longer supported by the PHP project, although vendors may provide backported security fixes.
>
> We strongly recommend upgrading to a currently supported PHP version and applying all available security updates. PHP 8.4.27 or later is recommended.

## Running locally

Run `composer install` to get all the dependencies.
Then you can run tests `php vendor/phpunit/phpunit/phpunit`

### Running tests in Docker

Run this in the project folder (use `8.5-cli` instead of `8.4-cli` to test with PHP 8.5).
The command works in bash (Linux, macOS) and PowerShell.

```bash
docker run --rm -v "${PWD}:/app" -w /app -e COMPOSER_ALLOW_SUPERUSER=1 php:8.4-cli bash -c '
  apt-get update -qq \
  && apt-get install -y -qq libgmp-dev libicu-dev unzip > /dev/null \
  && docker-php-ext-install gmp intl > /dev/null \
  && curl -sS https://getcomposer.org/installer | php -- --quiet --install-dir=/usr/local/bin --filename=composer \
  && composer install --no-interaction --no-progress \
  && vendor/bin/phpunit'
```

## Demo application 

There is a [demo application](https://github.com/SK-EID/mid-rest-php-demo) that you can run locally. 

## Features

* Simple interface for mobile-id authentication
* Pulling user's signing certificate 

This PHP client cannot be used to create digitally signed containers as 
there no library like [DigiDoc4J](https://github.com/open-eid/digidoc4j) exists for PHP.

## Requirements
 
* PHP 8.4.27 or later. Keep PHP up to date with the security updates of your PHP vendor.
  Integrators that are still on PHP 8.2 or 8.3 (patched to 8.2.35 or 8.3.36 or later) can keep using version 1.2.1 of this library.
* [PHP must be compiled with GMP support by using the --with-gmp option](https://www.php.net/manual/en/gmp.installation.php)
 
## Installation
 
The recommended way to install Mobile-ID PHP Client is through [Composer](https://getcomposer.org/)
 
 ```
 composer require sk-id-solutions/mobile-id-php-client "<VERSION>"
 ```
 
## How to use it

Here are examples of authentication with Mobile-ID PHP client

### You need to have Composer auto loading available for your application

```PHP
require_once __DIR__ . '/vendor/autoload.php';
```

### Example of authentication

<!-- Do not change code samples here but instead copy from ReadmeTest.documentAuthenticationProcess() -->

// See [ReadmeTest.php](blob/master/tests/ReadmeTest.php) for list of classes to 'use'
```PHP
    
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
        echo "User is not a MID client or user's certificates are revoked.";
    }
    catch (MidUnauthorizedException $e) {
        echo 'Integration error with Mobile-ID. Invalid MID credentials';
    }
    catch (MissingOrInvalidParameterException $e) {
        echo 'Problem with MID integration';
    }
    catch (MidInternalErrorException $e) {
        echo 'MID internal error';
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
        echo "User cancelled operation from his/her phone.";
    }
    catch (MidNotMidClientException $e) {
        echo "User is not a MID client or user's certificates are revoked.";
    }
    catch (MidSessionTimeoutException $e) {
        echo "User did not type in PIN code or communication error.";
    }
    catch (MidPhoneNotAvailableException $e) {
        echo "Unable to reach phone/SIM card. User needs to check if phone has coverage.";
    }
    catch (MidDeliveryException $e) {
        echo "Error communicating with the phone/SIM card.";
    }
    catch (MidInvalidUserConfigurationException $e) {
        echo "Mobile-ID configuration on user's SIM card differs from what is configured on service provider's side. User needs to contact his/her mobile operator.";
    }
    catch (MidSessionNotFoundException | MissingOrInvalidParameterException | MidUnauthorizedException | MidSslException $e) {
        throw new RuntimeException("Integrator-side error with MID integration or configuration. Error code:". $e->getCode());
    }
    catch (MidServiceUnavailableException $e) {
        echo "MID service is currently unavailable. User shold try again later.";
    }
    catch (MidInternalErrorException $internalError) {
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
```
See [mid-rest-php-demo](https://github.com/SK-EID/mid-rest-php-demo) for a more detailed real-world example.


# Long-polling configuration

You have two options for asking status of authentication session.
You can configure long polling which means that the server doesn't respond
immediately to session status request but waits until there is input from user (User has entered PIN1 or pressed cancel)
or if there is a timeout. However, this blocks the thread on caller's side and may be unwanted.
For this there is also option to withPollingSleepTimeoutSeconds(2) which means that the client
keeps making requests towards the server every 2 seconds.

If you don't set a positive value either to longPollingTimeoutSeconds or pollingSleepTimeoutSeconds
then pollingSleepTimeoutSeconds defaults to value 3 seconds.

## With long-polling

```PHP
    $this->client = MobileIdClient::newBuilder()
        ->withHostUrl("https://...")
        ->withRelyingPartyUUID("...")
        ->withRelyingPartyName("...")
        ->withSslPinnedPublicKeys("sha256//...")
        ->withLongPollingTimeoutSeconds(60)
        ->build();
```

## Without long-polling

```PHP
    $this->client = MobileIdClient::newBuilder()
        ->withHostUrl("https://...")
        ->withRelyingPartyUUID("...")
        ->withRelyingPartyName("...")
        ->withSslPinnedPublicKeys("sha256//...")
        ->withPollingSleepTimeoutSeconds(2)
        ->build();

```

# Checking if MID API host is trusted

When negotiating SSL connection with MID API, the MID server sends a certificate indicating its identity.
A public key is extracted from this certificate and sha256 hash of the public key is calculated.
This hash must exactly match with one of the hashes provided to this library:


```PHP

    $this->client = MobileIdClient::newBuilder()
        ->withHostUrl("https://...")
        ->withRelyingPartyUUID("...")
        ->withRelyingPartyName("...")
        ->withSslPinnedPublicKeys("sha256//hash-of-current-mid-api-ssl-host-public-key;sha256//hash-of-future-mid-api-ssl-host-public-key")
        ->build();
        

 ```


Otherwise, the connection to MID API is aborted before sending or receiving any data.

Internally the library uses https://curl.se/libcurl/c/CURLOPT_PINNEDPUBLICKEY.html for this.

## Obtaining digest of production API endpoint certificate
Open https://www.skidsolutions.eu/en/repository/certs/ 
And download mid.sk.ee certificate in PEM format and save it as "mid_sk_ee.PEM.cer".

```bash
openssl x509 -in mid_sk_ee.PEM.cer -pubkey -noout > mid.sk.ee.pubkey.pem
openssl asn1parse -noout -inform pem -in mid.sk.ee.pubkey.pem -out mid.sk.ee.pubkey.der
openssl dgst -sha256 -binary mid.sk.ee.pubkey.der | openssl base64
```
Copy the output (something like "fqp7yWK7iGGKj+3unYdm2DA3VCPDkwtyX+DrdZYSC6o=" and add "sha256//" in front of it)
so the outcome would be: "sha256//fqp7yWK7iGGKj+3unYdm2DA3VCPDkwtyX+DrdZYSC6o="



## Adding future production certificate

About once a year the server's SSL certificate gets switched.
All RP-s get a notification by e-mail from SK when this is going to happen.
Download new certificate and calculate its sha-256 digest (using instructions above) and add the digest to the list
by separating it with a semicolon. So the value is going to be something like this:

"sha256//fqp7yWK7iGGKj+3unYdm2DA3VCPDkwtyX+DrdZYSC6o=;sha256//digest-of-future-prod-certificate"

## Obtaining digest of demo API endpoint certificate

Demo server (tsp.demo.sk.ee) certificate is be available here: https://www.skidsolutions.eu/en/Repository/certs/certificates-for-testing
or you can download it directly from server.

```bash
openssl s_client -servername tsp.demo.sk.ee -connect tsp.demo.sk.ee:443 < /dev/null | sed -n "/-----BEGIN/,/-----END/p" > tsp.demo.sk.ee.pem
openssl x509 -in tsp.demo.sk.ee.pem -pubkey -noout > tsp.demo.sk.ee.pubkey.pem
openssl asn1parse -noout -inform pem -in tsp.demo.sk.ee.pubkey.pem -out tsp.demo.sk.ee.pubkey.der
openssl dgst -sha256 -binary tsp.demo.sk.ee.pubkey.der | openssl base64
```

# Setting public IP or interface

Sometimes the server has multiple network interfaces or IP addresses and the client
needs to specify which one to use for MID requests. This can be done using withNetworkInterface() paramter.

```PHP
    $this->client = MobileIdClient::newBuilder()
        ->withHostUrl("https://...")
        ->withRelyingPartyUUID("...")
        ->withRelyingPartyName("...")
        ->withSslPinnedPublicKeys("sha256//...")
        ->withNetworkInterface("10.11.12.13")
        ->build();
```

Internally this sets [CURLOPT_INTERFACE flag](https://curl.se/libcurl/c/CURLOPT_INTERFACE.html)


# Pulling user's signing certificate

This client also supports downloading user's mobile-id signing certificate.

 ```PHP
   $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
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
        echo "You are not a Mobile-ID client or your Mobile-ID certificates are revoked. Please contact your mobile operator.";
    }
    catch (MissingOrInvalidParameterException | MidUnauthorizedException $e) {
        throw new RuntimeException("Client side error with mobile-ID integration. Error code:". $e->getCode());
    }
    catch (MidInternalErrorException $internalError) {
        echo "Something went wrong with Mobile-ID service";
    }
 ```

# Signing

Signing is not supported with PHP library.

# Set up logging

Look into src/Util/Logger.php
The most basic option is to add  
```PHP
    echo $message."\n";
```
into debug_to_console() method.
