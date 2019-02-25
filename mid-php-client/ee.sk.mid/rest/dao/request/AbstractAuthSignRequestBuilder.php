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
require_once __DIR__ . '/../../../util/Logger.php';
require_once __DIR__ . '/../../../exception/ParameterMissingException.php';
abstract class AbstractAuthSignRequestBuilder
{
    public static $logger;

    private $relyingPartyName;
    private $relyingPartyUUID;
    private $phoneNumber;
    private $nationalIdentityNumber;
    private $hashToSign;
    private $language;
    private $displayText;
    private $displayTextFormat;

    public function __construct()
    {
        self::$logger = new Logger('AbstractAuthSignRequestBuilder');
    }

    protected function withRelyingPartyUUID($relyingPartyUUID)
    {
        $this->relyingPartyUUID = $relyingPartyUUID;
        return $this;
    }

    protected function withRelyingPartyName($relyingPartyName)
    {
        $this->relyingPartyName = $relyingPartyName;
        return $this;
    }

    protected function withPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    protected function withNationalIdentityNumber($nationalIdentityNumber)
    {
        $this->nationalIdentityNumber = $nationalIdentityNumber;
        return $this;
    }

    protected function withHashToSign($hashToSign)
    {
        $this->hashToSign = $hashToSign;
        return $this;
    }

    protected function withLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    protected function withDisplayText($displayText)
    {
        $this->displayText = $displayText;
        return $this;
    }

    protected function withDisplayTextFormat($displayTextFormat)
    {
        $this->displayTextFormat = $displayTextFormat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelyingPartyName()
    {
        return $this->relyingPartyName;
    }

    /**
     * @return mixed
     */
    public function getRelyingPartyUUID()
    {
        return $this->relyingPartyUUID;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getNationalIdentityNumber()
    {
        return $this->nationalIdentityNumber;
    }

    protected function getHashType()
    {
        return $this->hashToSign->getHashType();
    }

    protected function getHashInBase64()
    {
        return $this->hashToSign->getHashInBase64();
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return mixed
     */
    public function getDisplayText()
    {
        return $this->displayText;
    }

    /**
     * @return mixed
     */
    public function getDisplayTextFormat()
    {
        return $this->displayTextFormat;
    }

    protected function validateParameters()
    {
        if (empty($this->phoneNumber) || empty($this->nationalIdentityNumber)) {
            self::$logger->error('Phone number and national identity must be set');
            throw new ParameterMissingException('Phone number and national identity must be set');
        }
    }

    protected function validateExtraParameters()
    {
        if (is_null($this->hashToSign)) {
            throw new ParameterMissingException("hashToSign must be set");
        }

        if (is_null($this->language)) {
            throw new ParameterMissingException("Language for user dialog in mobile phone must be set");
        }
    }


}
