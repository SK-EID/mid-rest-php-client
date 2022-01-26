# Changelog
All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.2] - 2022-01-??

### Changed
- minimal PHP version lifted to 7.4
- added PHP 8 support

## [1.1] - 2021-01-14

### Changed
- SSL pinning
    - Relying Party must keep the list of trusted SSL public keys and supply them to mid-rest-php-client using withSslPinnedPublicKeys()
    - MidAuthenticationResponseValidator now takes trusted certificates info as constructor parameter.

- renamed
    - UserCancellationException -> MidUserCancellationException
    - NotMidClientException -> MidNotMidClientException
    - PhoneNotAvailableException -> MidPhoneNotAvailableException
    - DeliveryException -> MidDeliveryException
    - InvalidUserConfigurationException -> MidInvalidUserConfigurationException
    - UnauthorizedException -> MidUnauthorizedException
    - InvalidPhoneNumberException -> MidInvalidPhoneNumberException
    - InvalidNationalIdentityNumberException -> MidInvalidNationalIdentityNumberException
    - MobileIdException -> MidException
- moved Builders classes to their own files  


### Added
- MidServiceUnavailableException for handling 503 (Service Unavailable) exceptions
- MidSslException to show problems with  pinning
- MidClient can now specify withNetworkInterface("static.ip.or.eth.interface") 

### Removed
- withLiveEnvCertificates() and withDemoEnvCertificates() methods are now removed (certificates are not longer hard coded into client library)
- Removed handling "NOT_ACTIVE" certificate status as it is never return by MID API (API always returns NOT_MID_CLIENT instead)
- MidClient method withNetworkConnectionConfig (as it didn't do anything)
- CertificateNotTrustedException (replaced with MidInternalErrorException)

### Changes in libraries
- hrobertson/x509-verify internally replaced with sop/x509

## [1.0] - initial version

