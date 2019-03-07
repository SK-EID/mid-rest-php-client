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
namespace Sk\Mid\Rest\Dao\Request;
use JsonSerializable;
use \sk\mid\DisplayTextFormat;
use Sk\Mid\Language\Language;
class AuthenticationRequest extends AbstractRequest implements JsonSerializable
{
    /**
     * @var string $phoneNumber
     */
    private $phoneNumber;

    /**
     * @var string $nationalIdentityNumber
     */
    private $nationalIdentityNumber;

    /** @var string hash */
    private $hash;

    /** @var string $hashType */
    private $hashType;

    /** @var Language $language */
    private $language;

    /** @var string $displayText */
    private $displayText;

    /** @var DisplayTextFormat $displayTextFormat */
    private $displayTextFormat;

    public function __construct()
    {
        parent::__construct();
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getNationalIdentityNumber(): string
    {
        return $this->nationalIdentityNumber;
    }

    public function setNationalIdentityNumber(string $nationalIdentityNumber): void
    {
        $this->nationalIdentityNumber = $nationalIdentityNumber;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getHashType(): string
    {
        return $this->hashType;
    }

    public function setHashType(string $hashType): void
    {
        $this->hashType = $hashType;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    public function getDisplayText(): ?string
    {
        return $this->displayText;
    }

    public function setDisplayText(?string $displayText): void
    {
        $this->displayText = $displayText;
    }

    public function getDisplayTextFormat(): ?string
    {
        return $this->displayTextFormat;
    }

    public function setDisplayTextFormat(?string $displayTextFormat): void
    {
        $this->displayTextFormat = $displayTextFormat;
    }

    public static function newBuilder() : AuthenticationRequestBuilder
    {
        return new AuthenticationRequestBuilder();
    }


    public function jsonSerialize() : array {
        $params = [
                'phoneNumber' => $this->getPhoneNumber(),
                'nationalIdentityNumber' => $this->getNationalIdentityNumber(),
                'relyingPartyUUID' => $this->getRelyingPartyUUID(),
                'relyingPartyName' => $this->getRelyingPartyName(),
                'hash' => $this->getHash(),
                'hashType' => $this->getHashType(),
                'language' => (string) $this->getLanguage()
        ];

        if (null !== $this->getDisplayText()) {
            $params['displayText'] = $this->getDisplayText();

            if (null !== $this->getDisplayTextFormat()) {
                $params['displayTextFormat'] = $this->getDisplayTextFormat();
            }
        }
        return $params;
    }

}
