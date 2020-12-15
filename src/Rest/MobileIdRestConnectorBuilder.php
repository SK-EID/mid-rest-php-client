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
namespace Sk\Mid\Rest;

class MobileIdRestConnectorBuilder
{
    const
            PIN_SHA256_VALID_FROM_2019_03_21_TO_2021_03_25 = "sha256//fqp7yWK7iGGKj+3unYdm2DA3VCPDkwtyX+DrdZYSC6o=",
            DEMO_PIN_SHA256_VALID_FROM_2019_01_02_TO_2020_01_07 = "sha256//XgrOHbcGDbQJaXjL9ISo+y7bsXAcVOLLEzeeNO6BXDM=";

    /** @var string $endpointUrl */
    private $endpointUrl;

    /** @var string $clientConfig */
    private $clientConfig;

    /** @var string $relyingPartyUUID */
    private $relyingPartyUUID;

    /** @var string $relyingPartyName */
    private $relyingPartyName;

    /** @var array $customHeaders */
    private $customHeaders = array();

    private $sslPublicKeys = self::PIN_SHA256_VALID_FROM_2019_03_21_TO_2021_03_25.";".self::DEMO_PIN_SHA256_VALID_FROM_2019_01_02_TO_2020_01_07;

    public function getEndpointUrl() : ?string
    {
        return $this->endpointUrl;
    }

    public function getClientConfig() : ?string
    {
        return $this->clientConfig;
    }

    public function getRelyingPartyUUID() : ?string
    {
        return $this->relyingPartyUUID;
    }

    public function getRelyingPartyName() : ?string
    {
        return $this->relyingPartyName;
    }

    /**
     * @return array
     */
    public function getCustomHeaders(): ?array
    {
        return $this->customHeaders;
    }

    /**
     * @return string
     */
    public function getSslPublicKeys(): string
    {
        return $this->sslPublicKeys;
    }

    public function withEndpointUrl(?string $endpointUrl) : MobileIdRestConnectorBuilder
    {
        $this->endpointUrl = $endpointUrl;
        return $this;
    }

    public function withClientConfig(?string $clientConfig) : MobileIdRestConnectorBuilder
    {
        $this->clientConfig = $clientConfig;
        return $this;
    }

    public function withRelyingPartyUUID(?string $relyingPartyUUID) : MobileIdRestConnectorBuilder
    {
        $this->relyingPartyUUID = $relyingPartyUUID;
        return $this;
    }

    public function withRelyingPartyName(?string $relyingPartyName) : MobileIdRestConnectorBuilder
    {
        $this->relyingPartyName = $relyingPartyName;
        return $this;
    }

    public function withCustomHeaders(?array $customHeaders) : MobileIdRestConnectorBuilder
    {
        $this->customHeaders = $customHeaders;
        return $this;
    }

    public function withSslPublicKeys(string $publicKeys) : MobileIdRestConnectorBuilder
    {
        $this->sslPublicKeys = $publicKeys;
        return $this;
    }

    public function withDemoEnvPublicKeys() : MobileIdRestConnectorBuilder
    {
        $this->sslPublicKeys = self::DEMO_PIN_SHA256_VALID_FROM_2019_01_02_TO_2020_01_07;
        return $this;
    }

    public function withLiveEnvPublicKeys() : MobileIdRestConnectorBuilder
    {
        $this->sslPublicKeys = self::PIN_SHA256_VALID_FROM_2019_03_21_TO_2021_03_25;
        return $this;
    }

    public function build() : MobileIdRestConnector
    {
        return new MobileIdRestConnector($this);
    }
}
