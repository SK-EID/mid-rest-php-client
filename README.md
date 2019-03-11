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

```
require_once __DIR__ . '/vendor/autoload.php';
```

### Example of configuring the client

```
use Sk\Mid\MobileIdClient;
use Sk\Middemo\Model\UserMidSession;

/** @var string $midRelyingPartyUuid */
private $midRelyingPartyUuid = '00000000-0000-0000-0000-000000000000';

/** @var string $midRelyingPartyName */
private $midRelyingPartyName = 'DEMO';

/** @var string $midApplicationProviderHost */
private $midApplicationProviderHost = 'https://tsp.demo.sk.ee/mid-api';

public function mobileIdClient() : MobileIdClient
{
    return MobileIdClient::newBuilder()
        ->withRelyingPartyUUID($this->midRelyingPartyUuid)
        ->withRelyingPartyName($this->midRelyingPartyName)
        ->withHostUrl($this->midApplicationProviderHost)
        ->build();
}
```

### Example of authentication

#### Creating an authentication request

```
$userRequest = $authenticationSessionInfo->getUserRequest();
$authenticationHash = $authenticationSessionInfo->getAuthenticationHash();
$request = AuthenticationRequest::newBuilder()
    ->withRelyingPartyUUID($this->client->getRelyingPartyUUID())
    ->withRelyingPartyName($this->client->getRelyingPartyName())
    ->withPhoneNumber($userRequest->getPhoneNumber())
    ->withNationalIdentityNumber($userRequest->getNationalIdentityNumber())
    ->withHashToSign($authenticationHash)
    ->withLanguage(ENG::asType())
    ->withDisplayText($this->midAuthDisplayText)
    ->withDisplayTextFormat('GSM7')
    ->build();
```

#### Getting the authentication response

```
$authenticationResult = null;
try {

    $response = $this->client->getMobileIdConnector()->authenticate($request);
    $sessionStatus = $this->client->getSessionStatusPoller()->fetchFinalSessionStatus(
        $response->getSessionId()
    );

    $authentication = $this->client->createMobileIdAuthentication($sessionStatus, $authenticationHash);
    $validator = new AuthenticationResponseValidator();
    $authenticationResult = $validator->validate($authentication);
} catch (NotMidClientException $e) {
    throw new MidAuthException($e->getMessage());
}
if (!$authenticationResult->isValid()) {
    throw new MidAuthException($authenticationResult->getErrors());
}
return $authenticationResult->getAuthenticationIdentity();
```
