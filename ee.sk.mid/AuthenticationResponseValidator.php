<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2019 SK ID Solutions AS
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
require_once __DIR__ . '/util/Logger.php';
require_once __DIR__ . '/exception/MidInternalErrorException.php';
require_once 'MobileIdAuthenticationResult.php';
require_once 'MobileIdAuthenticationError.php';
require_once 'AuthenticationIdentity.php';
require_once 'CertificateParser.php';

class AuthenticationResponseValidator
{
    /** @var Logger $logger */
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('AuthenticationResponseValidator');
    }

    public function validate(Mobileidauthentication $authentication) : MobileIdAuthenticationResult
    {
        $this->validateAuthentication($authentication);
        $authenticationResult = new MobileIdAuthenticationResult();
        $identity = $this->constructAuthenticationIdentity($authentication->getCertificate());
        $authenticationResult->setAuthenticationIdentity($identity);
        if (!$this->isResultOk($authentication)) {
            $authenticationResult->setValid(false);
            $authenticationResult->addError(MobileIdAuthenticationError::INVALID_RESULT);
        }
//        if (!$this->isSignatureValid($authentication)) {
//            $authenticationResult->setValid(false);
//            $authenticationResult->addError(MobileIdAuthenticationError::SIGNATURE_VERIFICATION_FAILURE);
//        }
        if (!$this->isCertificateValid($authentication->getCertificate())) {
            $authenticationResult->setValid(false);
            $authenticationResult->addError(MobileIdAuthenticationError::CERTIFICATE_EXPIRED);
        }

        return $authenticationResult;
    }

    private function validateAuthentication(Mobileidauthentication $authentication) : void
    {
        if (is_null($authentication->getCertificate())) {
            $this->logger->error('Certificate is not present in the authentication response');
            throw new MidInternalErrorException('Certificate is not present in the authentication response');
        } else if (empty($authentication->getSignatureValueInBase64())) {
            $this->logger->error('Signature is not present in the authentication response');
            throw new MidInternalErrorException('Signature is not present in the authentication response');
        } else if (is_null($authentication->getHashType())) {
            $this->logger->error('Hash type is not present in the authentication response');
            throw new MidInternalErrorException('Hash type is not present in the authentication response');
        }
    }

    function constructAuthenticationIdentity(AuthenticationCertificate $certificate) : AuthenticationIdentity
    {
        $identity = new AuthenticationIdentity();
        $subject = $certificate->getSubject();
        try {
            $subjectReflection = new ReflectionClass($subject);
        } catch (ReflectionException $e) {
        }

        foreach ( $subjectReflection->getProperties() as $property )
        {
            $property->setAccessible( true );
            if ( strcasecmp( $property->getName(), 'GN' ) === 0 )
            {
                $identity->setGivenName( $property->getValue( $subject ) );
            }
            elseif ( strcasecmp( $property->getName(), 'SN' ) === 0 )
            {
                $identity->setSurName( $property->getValue( $subject ) );
            }
            elseif ( strcasecmp( $property->getName(), 'SERIALNUMBER' ) === 0 )
            {
                $identity->setIdentityCode( $property->getValue( $subject ) );
            }
            elseif ( strcasecmp( $property->getName(), 'C' ) === 0 )
            {
                $identity->setCountry( $property->getValue( $subject ) );
            }
        }

        return $identity;
    }

    private function getIdentityNumber(string $serialNumber) : string
    {
        return preg_replace('^PNO[A-Z][A-Z]-', '', $serialNumber);
    }

    private function isResultOk(MobileIdAuthentication $authentication) : bool
    {
        return strcasecmp('OK', $authentication->getResult()) == 0;
    }

//    private function isSignatureValid(MobileIdAuthentication $authentication) : bool
//    {
//        $preparedCertificate = CertificateParser::getPemCertificate( $authentication->getCertificate() );
//        $signature = $authentication->getValue();
//        $publicKey = openssl_pkey_get_public( $preparedCertificate );
//        if ( $publicKey !== false )
//        {
//            $data = $authentication->getSignedData();
//            return openssl_verify( $data, $signature, $publicKey, OPENSSL_ALGO_SHA512 ) === 1;
//        }
//        return false;
//    }

    private function isCertificateValid(AuthenticationCertificate $certificate) : bool
    {
        return !$certificate->getNotAfter()->before(new Date());
    }




}
