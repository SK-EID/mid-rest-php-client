# Mobile-ID (MID) PHP Rest Client

[![Build Status](https://api.travis-ci.org/SK-EID/mid-rest-php-client.svg?branch=master)](https://travis-ci.org/SK-EID/mid-rest-php-client)
[![Coverage Status](https://img.shields.io/codecov/c/github/SK-EID/mid-rest-php-client.svg)](https://codecov.io/gh/SK-EID/mid-rest-php-client)
[![License: MIT](https://img.shields.io/github/license/mashape/apistatus.svg)](https://opensource.org/licenses/MIT)

## Running locally

Run `composer install` to get all the dependencies.
Then you can run tests `php vendor/phpunit/phpunit/phpunit`

## Demo application 

There is a [demo application](https://github.com/SK-EID/mid-rest-php-demo) that you can run locally. 

## Features

* Simple interface for mobile-id authentication
* Pulling user's signing certificate 

This PHP client cannot be used to create digitally signed containers as 
there no library like [DigiDoc4J](https://github.com/open-eid/digidoc4j) exists for PHP.

## Requirements
 
* PHP 7.2 or later
 
## Installation
 
The recommended way to install Mobile-ID PHP Client is through [Composer](https://getcomposer.org/)
 
 ```
 composer require sk-id-solutions/mobile-id-php-client "~1.0"
 ```
 
## How to use it

Here are examples of authentication with Mobile-ID PHP client

### You need to have Composer auto loading available for your application

```PHP
require_once __DIR__ . '/vendor/autoload.php';
```

### Example of authentication


```PHP
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
```

In reality authentication cannot be handled by a single request to back-end
as there is need to display verification code to the user.
See the demo application for a more detailed real-world example.


## Long polling

If you don't set a positive value either to longPollingTimeoutSeconds or pollingSleepTimeoutSeconds
then pollingSleepTimeoutSeconds defaults to value 3 seconds.

## Certificates

The client also supports to ask for a user's mobile-id signing certificate.

 ```PHP

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

 ```

## Signing

Signing is not supported with PHP library.
