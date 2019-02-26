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
require_once 'AbstractRequest.php';
require_once 'CertificateRequestBuilder.php';
class CertificateRequest extends AbstractRequest implements JsonSerializable
{

    private $phoneNumber;

    private $nationalIdentityNumber;

    public function __construct()
    {
        parent::__construct();
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getNationalIdentityNumber()
    {
        return $this->nationalIdentityNumber;
    }

    public function setNationalIdentityNumber($nationalIdentityNumber)
    {
        $this->nationalIdentityNumber = $nationalIdentityNumber;
    }

    public function toString()
    {
        return "CertificateRequest{<br/>phoneNumber=" . $this->phoneNumber . ",<br/> nationalIdentityNumber=" . $this->nationalIdentityNumber . "<br/>}<br/><br/>";
    }

    public static function newBuilder()
    {
        return new CertificateRequestBuilder();
    }

    public function jsonSerialize() {
        return [
                'phoneNumber' => $this->getPhoneNumber(),
                'nationalIdentityNumber' => $this->getNationalIdentityNumber(),
                'relyingPartyUUID' => $this->getRelyingPartyUUID(),
                'relyingPartyName' => $this->getRelyingPartyName()
        ];
    }

}
