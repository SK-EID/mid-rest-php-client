# Mobile-ID (MID) PHP Rest Client


TODO! Replace SK-EID here before creating pull request!

[![Build Status](https://api.travis-ci.org/mikk125/mid-rest-php-client.svg?branch=master)](https://travis-ci.org/mikk125/mid-rest-php-client)
[![Coverage Status](https://img.shields.io/codecov/c/github/mikk125/mid-rest-php-client.svg)](https://codecov.io/gh/mikk125/mid-rest-php-client)
[![License: MIT](https://img.shields.io/github/license/mashape/apistatus.svg)](https://opensource.org/licenses/MIT)

## Running locally

Run `composer install` to get all the dependencies.
Then you can run tests `php vendor/phpunit/phpunit/phpunit`

## Demo application 

There is a [demo application](https://github.com/SK-EID/mid-rest-php-demo) that you can run locally. 

## Features

* Simple interface for use authentication

 ## Requirements
 
 * PHP 7.2 or later
 
 ## Installation
 
 The recommended way to install Mobile-ID PHP Client is through [Composer](https://getcomposer.org/)
 
 ```
 composer require sk-id-solutions/mobile-id-php-client "~1.0"
 ```
 
## How to use it

Here are examples of authentication with Mobile-ID PHP client

### Make it available for your application

``` PHP
require_once __DIR__ . '/vendor/autoload.php';
```

### Example of configuring the client

``` PHP
use Sk\Mid\MobileIdClient;
use Sk\Middemo\Model\UserMidSession;

public function mobileIdClient() : MobileIdClient
{
        return MobileIdClient::newBuilder()
            ->withRelyingPartyUUID('00000000-0000-0000-0000-000000000000')
            ->withRelyingPartyName('DEMO')
            ->withHostUrl('https://tsp.demo.sk.ee/mid-api')
            ->build();
}
```

### Example of authentication

#### Creating an authentication request

``` PHP
$userRequest = $authenticationSessionInfo->getUserRequest();
$authenticationHash = $authenticationSessionInfo->getAuthenticationHash();
$request = AuthenticationRequest::newBuilder()
    ->withPhoneNumber($userRequest->getPhoneNumber())
    ->withNationalIdentityNumber($userRequest->getNationalIdentityNumber())
    ->withHashToSign($authenticationHash)
    ->withLanguage(ENG::asType())
    ->withDisplayText($this->midAuthDisplayText)
    ->withDisplayTextFormat(DisplayTextFormat::GSM7)
    ->build();
```

#### Getting the authentication response

``` PHP
$authenticationResult = null;
try {

    $response = $this->client->getMobileIdConnector()->authenticate($request);
    $sessionStatus = $this->client->getSessionStatusPoller()->fetchFinalSessionStatus(
        $response->getSessionId()
    );

    $authentication = $this->client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    $validator = new AuthenticationResponseValidator();
    $authenticationResult = $validator->validate($authentication);
} catch (UserCancellationException $e) {
    throw new MidOperationException("You cancelled operation from your phone.");
} catch (NotMidClientException $e) {
    throw new MidOperationException("You are not a Mobile-ID client or your Mobile-ID certificates are revoked. Please contact your mobile operator.");
} catch (MidSessionTimeoutException $e) {
    throw new MidOperationException("You didn't type in PIN code into your phone or there was a communication error.");
} catch (PhoneNotAvailableException $e) {
    throw new MidOperationException("Unable to reach your phone. Please make sure your phone has mobile coverage.");
} catch (DeliveryException $e) {
    throw new MidOperationException("Communication error. Unable to reach your phone.");
} catch (InvalidUserConfigurationException $e) {
    throw new MidOperationException("Mobile-ID configuration on your SIM card differs from what is configured on service provider's side. Please contact your mobile operator.");
} catch (MidSessionNotFoundException | MissingOrInvalidParameterException | UnauthorizedException $e) {
    throw new MidOperationException("Client side error with mobile-ID integration.", $e->getCode());
} catch (MidInternalErrorException $e) {
    throw new MidOperationException("MID internal error", $e->getCode());
}

if (!$authenticationResult->isValid()) {
    throw new MidOperationException($authenticationResult->getErrors());
}
return $authenticationResult->getAuthenticationIdentity();
```


## Handling intentional exceptions

There are some exceptions, that are thrown, so that one who integrates this client can informatively
notify user of what happened. They correspond to [Expected results](https://github.com/SK-EID/MID/wiki/Test-number-for-automated-testing-in-DEMO#test-numbers-for-automated-testing) that MID service provides.

All of those exceptions extend one exception class called ```MobileIdException```.
Few of the exceptions to keep an eye for are:
* ```DeliveryException```
* ```NotMidClientException```
* ```PhoneNotAvailableException```

## Certificates

The client also supports to ask for a certificate.
 An example:
 ```PHP
$client = MobileIdClient::newBuilder()
   ->withRelyingPartyUUID("00000000-0000-0000-0000-000000000000")
   ->withRelyingPartyName("DEMO")
   ->withHostUrl("https://tsp.demo.sk.ee/mid-api")
   ->build();


$certRequest = CertificateRequest::newBuilder()
   ->withNationalIdentityNumber(60001019906)
   ->withPhoneNumber("+37200000766")
   ->build();

$resp = $client->getMobileIdConnector()->getCertificate($certRequest);
 ```

## Signing

Signing is not supported.
