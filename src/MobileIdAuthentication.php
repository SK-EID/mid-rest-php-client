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

use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\HashType\HashType;
use Sk\Mid\Rest\Dao\MidCertificate;

class MobileIdAuthentication
{

    /** @var string $result */
    private $result;

    /** @var string $signedHashInBase64 */
    private $signedHashInBase64;

    /** @var HashType $hashType */
    private $hashType;

    /** @var string $signatureValueInBase64 */
    private $signatureValueInBase64;

    /** @var string $algorithmName */
    private $algorithmName;

    /** @var MidCertificate $certificate */
    private $certificate;

    public function __construct(MobileIdAuthenticationBuilder $builder)
    {
        $this->result = $builder->getResult();
        $this->signedHashInBase64 = $builder->getSignedHashInBase64();
        $this->hashType = $builder->getHashType();
        $this->signatureValueInBase64 = $builder->getSignatureValueInBase64();
        $this->algorithmName = $builder->getAlgorithmName();
        $this->certificate = $builder->getCertificate();
    }

    public function getSignatureValue() : string
    {
        $decodedBase64 = base64_decode($this->signatureValueInBase64, true);
        if (false === $decodedBase64) {
            throw new MissingOrInvalidParameterException("Failed to parse signature value. Input is not valid Base64 string: '" . $this->signatureValueInBase64 . "'");
        } else {
            return $decodedBase64;
        }
    }

    public function getResult() : string
    {
        return $this->result;
    }

    public function getSignedHashInBase64() : string
    {
        return $this->signedHashInBase64;
    }

    public function getHashType() : ?HashType
    {
        return $this->hashType;
    }

    public function getSignatureValueInBase64() : ?string
    {
        return $this->signatureValueInBase64;
    }

    public function getAlgorithmName() : string
    {
        return $this->algorithmName;
    }

    public function getCertificate() : MidCertificate
    {
        return new MidCertificate($this->certificate);
    }

    public static function newBuilder() : MobileIdAuthenticationBuilder
    {
        return new MobileIdAuthenticationBuilder();
    }

    public function constructAuthenticationIdentity() : MidIdentity
    {
        return MidIdentity::parseFromCertificate($this->getCertificate());
    }

    public function getValidatedAuthenticationResult() : MobileIdAuthenticationResult
    {
        $authenticationResponseValidator = new AuthenticationResponseValidator();
        return $authenticationResponseValidator->validate($this);

    }

}

class MobileIdAuthenticationBuilder
{

    /** @var string $result */
    private $result;

    /** @var string $signedHashInBase64 */
    private $signedHashInBase64;

    /** @var HashType $hashType */
    private $hashType;

    /** @var string $signatureValueInBase64 */
    private $signatureValueInBase64;

    /** @var string $algorithmName */
    private $algorithmName;

    /** @var array $certificate */
    private $certificate;

    public function __construct()
    {
    }

    public function getResult() : ?string
    {
        return $this->result;
    }

    public function getSignedHashInBase64() : ?string
    {
        return $this->signedHashInBase64;
    }

    public function getHashType() : ?HashType
    {
        return $this->hashType;
    }

    public function getSignatureValueInBase64() : ?string
    {
        return $this->signatureValueInBase64;
    }

    public function getAlgorithmName() : ?string
    {
        return $this->algorithmName;
    }

    public function getCertificate() : ?array
    {
        return $this->certificate;
    }

    public function withResult(string $result) : MobileIdAuthenticationBuilder
    {
        $this->result = $result;
        return $this;
    }

    public function withSignedHashInBase64(string $signedHashInBase64) : MobileIdAuthenticationBuilder
    {
        $this->signedHashInBase64 = $signedHashInBase64;
        return $this;
    }

    public function withHashType(?HashType $hashType) : MobileIdAuthenticationBuilder
    {
        $this->hashType = $hashType;
        return $this;
    }

    public function withSignatureValueInBase64(string $signatureValueInBase64) : MobileIdAuthenticationBuilder
    {
        $this->signatureValueInBase64 = $signatureValueInBase64;
        return $this;
    }

    public function withAlgorithmName(string $algorithmName) : MobileIdAuthenticationBuilder
    {
        $this->algorithmName = $algorithmName;
        return $this;
    }

    public function withCertificate(?array $certificate) : MobileIdAuthenticationBuilder
    {
        $this->certificate = $certificate;
        return $this;
    }

    public function build(): MobileIdAuthentication
    {
        return new MobileIdAuthentication($this);
    }


}
