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
namespace Sk\Mid;
use InvalidArgumentException;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Exception\MidNotMidClientException;
use Sk\Mid\Rest\Dao\MidCertificate;
use Sk\Mid\Util\Logger;
use Sop\CryptoEncoding\PEM;
use Sop\X509\Certificate\Certificate;
use UnexpectedValueException;


class AuthenticationResponseValidator
{
    /** @var Logger $logger */
    private Logger $logger;

    /** @var array $certificatePath */
    private $trustedCaCertificates;

    public function __construct(AuthenticationResponseValidatorBuilder $builder)
    {
        $this->logger = new Logger('AuthenticationResponseValidator');

        if (empty($builder->getTrustedCaCertificates())) {
            throw new InvalidArgumentException("You need to set at least one trusted CA certificate to builder");
        }

        $this->trustedCaCertificates = $builder->getTrustedCaCertificates();
    }

    public function validate(Mobileidauthentication $authentication) : MobileIdAuthenticationResult
    {
        $this->validateAuthentication($authentication);
        $authenticationResult = new MobileIdAuthenticationResult();

        if (!$this->isResultOk($authentication)) {
            throw new MidInternalErrorException($authenticationResult->getErrorsAsString());
        }
        if ( !$this->verifyCertificateExpiry( $authentication->getCertificate() ) ) {
            throw new MidNotMidClientException();
        }
        if ( !$this->verifyCertificateTrusted( $authentication->getCertificateX509() ) ) {
            throw new MidInternalErrorException(MobileIdAuthenticationError::CERTIFICATE_NOT_TRUSTED );
        }

        $identity = $authentication->constructAuthenticationIdentity();
        $authenticationResult->setAuthenticationIdentity($identity);

        return $authenticationResult;
    }

    private function validateAuthentication(Mobileidauthentication $authentication) : void
    {
        if (is_null($authentication->getCertificate())) {
            throw new MidInternalErrorException('Certificate is not present in the authentication response');
        } else if (empty($authentication->getSignatureValueInBase64())) {
            throw new MidInternalErrorException('Signature is not present in the authentication response');
        } else if (is_null($authentication->getHashType())) {
            throw new MidInternalErrorException('Hash type is not present in the authentication response');
        }
    }

    private function isResultOk(MobileIdAuthentication $authentication) : bool
    {
        return strcasecmp('OK', $authentication->getResult()) == 0;
    }

    private function verifyCertificateExpiry(MidCertificate $authenticationCertificate ): bool
    {
        return $authenticationCertificate->getValidTo() > time();
    }

    private function verifyCertificateTrusted($certificate): bool
    {
        foreach ($this->trustedCaCertificates as $trustedCaCertificate) {
            $cert = Certificate::fromPEM(PEM::fromString($certificate['certificateAsString']));
            $ca = Certificate::fromPEM(PEM::fromString($trustedCaCertificate));

            try {
                if ($cert->verify($ca->tbsCertificate()->subjectPublicKeyInfo())) {
                    return true;
                }
            } catch (UnexpectedValueException $e) {
                continue;
            }
        }
        return false;
    }

    public static function newBuilder(): AuthenticationResponseValidatorBuilder
    {
        return new AuthenticationResponseValidatorBuilder();
    }

}
