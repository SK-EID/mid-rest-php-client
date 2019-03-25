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
use ReflectionClass;
use ReflectionException;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Rest\Dao\MidCertificate;

class MidIdentity
{

    /** @var string $givenName */
    private $givenName;

    /** @var string $surName */
    private $surName;

    /** @var string $identityCode */
    private $identityCode;

    /** @var string $country */
    private $country;

    public function __construct()
    {
    }

    public function getGivenName() : string
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName)
    {
        $this->givenName = $givenName;
    }

    public function getSurName() : string
    {
        return $this->surName;
    }

    public function setSurName(string $surName)
    {
        $this->surName = $surName;
    }

    public function getIdentityCode() : string
    {
        return $this->identityCode;
    }

    public function setIdentityCode(string $identityCode)
    {
        $this->identityCode = $identityCode;
    }

    public function getCountry() : string
    {
        return $this->country;
    }

    public function setCountry(string $country)
    {
        $this->country = $country;
    }
    
    public function toString() : string
    {
        return "AuthenticationIdentity{<br/>givenName=" . $this->givenName . ",<br/> surName=" . $this->surName . ",<br/> identityCode=" . $this->identityCode . ",<br/> country=" . $this->country . "<br/>)<br/><br/>";
    }

    public static function parseFromRawCertificate(array $certificate) : MidIdentity
    {
        $authenticationCertificate = new MidCertificate($certificate);
        return self::parseFromCertificate($authenticationCertificate);
    }

    public static function parseFromCertificate(MidCertificate $certificate) : MidIdentity
    {

        $identity = new MidIdentity();
        $subject = $certificate->getSubject();
        try {
            $subjectReflection = new ReflectionClass($subject);
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
                    $identityCode = $property->getValue( $subject );
                    $identity->setIdentityCode(preg_replace('(^PNO[A-Z][A-Z]-)','',$identityCode));
                }
                elseif ( strcasecmp( $property->getName(), 'C' ) === 0 )
                {
                    $identity->setCountry( $property->getValue( $subject ) );
                }
            }
        } catch (ReflectionException $e) {
            throw new MidInternalErrorException("Error parsing certificate");
        }
        return $identity;
    }

}
