# Changelog
All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.1 - Unreleased] - 2020-12-??

### Changed
- pinning

- Relying Party must keep the list of trusted SSL public keys and supply them to mid-rest-php-client using withSslPinnedPublicKeys()
    - See...
    (and update this file when new certificates are published and eventually replaced by Application Provider (SK)
    - withLiveEnvCertificates() and withDemoEnvCertificates() methods are now removed (certificates are not longer hard coded into client library)
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

### Added
- MidServiceUnavailableException for handling 503 (Service Unavailable) exceptions
- MidSslException to show problems with pinning

### Removed
- Removed handling "NOT_ACTIVE" certificate status as it is never return by MID API (API always returns NOT_MID_CLIENT instead)

- CertificateNotTrustedException (replaced with MidInternalErrorException)


## [1.0] - initial version

