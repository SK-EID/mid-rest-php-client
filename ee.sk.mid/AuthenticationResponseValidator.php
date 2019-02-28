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
require_once __DIR__ . '/exception/TechnicalErrorException.php';
require_once 'MobileIdAuthenticationResult.php';
require_once 'MobileIdAuthenticationError.php';
require_once 'AuthenticationIdentity.php';
require_once 'CertificateParser.php';
class AuthenticationResponseValidator
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('AuthenticationResponseValidator');
    }

    public function validate($authentication)
    {
        $this->validateAuthentication($authentication);
        $authenticationResult = new MobileIdAuthenticationResult();
        $identity = $this->constructAuthenticationIdentity($authentication->getCertificate());
        $authenticationResult->setAuthenticationIdentity($identity);
        if (!$this->isResultOk($authentication)) {
            $authenticationResult->setValid(false);
            $authenticationResult->addError(MobileIdAuthenticationError::INVALID_RESULT);
        }
        if (!$this->isSignatureValid($authentication)) {
            $authenticationResult->setValid(false);
            $authenticationResult->addError(MobileIdAuthenticationError::SIGNATURE_VERIFICATION_FAILURE);
        }
        if (!$this->isCertificateValid($authentication->getCertificate())) {
            $authenticationResult->setValid(false);
            $authentication->addError(MobileIdAuthenticationError::CERTIFICATE_EXPIRED);
        }

        return $authenticationResult;
    }

    private function validateAuthentication($authentication)
    {
        if (is_null($authentication->getCertificate())) {
            $this->logger->error('Certificate is not present in the authentication response');
            throw new TechnicalErrorException('Certificate is not present in the authentication response');
        } else if (empty($authentication->getSignatureValueInBase64())) {
            $this->logger->error('Signature is not present in the authentication response');
            throw new TechnicalErrorException('Signature is not present in the authentication response');
        } else if (is_null($authentication->getHashType())) {
            $this->logger->error('Hash type is not present in the authentication response');
            throw new TechnicalErrorException('Hash type is not present in the authentication response');
        }
    }

    function constructAuthenticationIdentity($certificate)
    {
        // TODO tuleb ümber teha.
        $identity = new AuthenticationIdentity();
//        $identity = new AuthenticationIdentity();
//        $ln = new LdapName($certificate->getSubjectDN()->getName());
//        $var4 = $ln->getRdns()->iterator();
//        while ($var4->hasNext())
//        {
//            $rdn = $var4->next();
//            $type = $rdn->getType()->getUpperCase();
//            $var8 = -1;
//            switch ($type->hashCode())
//            {
//                case -1135010629:
//                    if ($type == "SURNAME") {
//                        $var8 = 1;
//                    }
//                    break;
//                case -977765827:
//                    if ($type == "SERIALNUMBER") {
//                        $var8 = 2;
//                    }
//                    break;
//                case -38372504:
//                    if ($type == "GIVENNAME") {
//                        $var8 = 0;
//                    }
//                    break;
//                case 67:
//                    if ($type == "C") {
//                        $var8 = 3;
//                    }
//            }
//
//            switch ($var8)
//            {
//                case 0:
//                    $identity->setGivenName($rdn->getValue()->toString());
//                    break;
//                case 1:
//                    $identity->setSurName($rdn->getValue()->toString());
//                    break;
//                case 2:
//                    $identity->setIdentityCode($this->getIdentityNumber($rdn->getValue()->toString()));
//                    break;
//                case 3:
//                    $identity->setCountry($rdn->getValue()->toString());
//            }
//        }
//        return $identity;
    }

    private function getIdentityNumber($serialNumber)
    {
        return preg_replace('^PNO[A-Z][A-Z]-', '', $serialNumber);
    }

    private function isResultOk($authentication)
    {
        return strcasecmp('OK', $authentication->getResult()) == 0;
    }

    private function isSignatureValid($authentication)
    {
        $preparedCertificate = CertificateParser::getPemCertificate( $authentication->getCertificate() );
        $signature = $authentication->getValue();
        $publicKey = openssl_pkey_get_public( $preparedCertificate );
        if ( $publicKey !== false )
        {
            $data = $authentication->getSignedData();
            return openssl_verify( $data, $signature, $publicKey, OPENSSL_ALGO_SHA512 ) === 1;
        }
        return false;
    }

    private function isCertificateValid($certificate)
    {
        return !$certificate->getNotAfter()->before(new Date());
    }




}
