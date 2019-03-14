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
namespace Sk\Mid;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Exception\NotMidClientException;
use Sk\Mid\Rest\Dao\MidCertificate;
use Sk\Mid\Util\Logger;

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

        if (!$this->isResultOk($authentication)) {
            $authenticationResult->setValid(false);
            $authenticationResult->addError(MobileIdAuthenticationError::INVALID_RESULT);
            throw new MidInternalErrorException($authenticationResult->getErrorsAsString());
        }
        if ( !$this->verifyCertificateExpiry( $authentication->getCertificate() ) ) {
            $authenticationResult->setValid( false );
            $authenticationResult->addError( MobileIdAuthenticationError::CERTIFICATE_EXPIRED );
            throw new NotMidClientException();
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

    private function verifyCertificateExpiry(MidCertificate $authenticationCertificate )
    {
        return $authenticationCertificate !== null && $authenticationCertificate->getValidTo() > time();
    }

}
