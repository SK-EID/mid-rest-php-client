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
namespace Sk\Mid\Rest;

class MobileIdRestConnectorBuilder
{
    /** @var ?string $endpointUrl */
    private ?string $endpointUrl = null;

    /** @var ?string $networkInterface */
    private ?string $networkInterface = null;

    /** @var ?string $relyingPartyUUID */
    private ?string $relyingPartyUUID = null;

    /** @var ?string $relyingPartyName */
    private ?string $relyingPartyName = null;

    /** @var array $customHeaders */
    private array $customHeaders = array();

    private ?string $sslPinnedPublicKeys;

    public function getEndpointUrl() : ?string
    {
        return $this->endpointUrl;
    }

    public function getNetworkInterface() : ?string
    {
        return $this->networkInterface;
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
    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }

    /**
     * @return string
     */
    public function getSslPinnedPublicKeys(): ?string
    {
        return $this->sslPinnedPublicKeys;
    }

    public function isSslPinnedPublicKeysSet(): bool
    {
        return $this->sslPinnedPublicKeys != null;
    }

    public function withEndpointUrl(?string $endpointUrl) : MobileIdRestConnectorBuilder
    {
        $this->endpointUrl = $endpointUrl;
        return $this;
    }

    public function withNetworkInterface(?string $networkInterface) : MobileIdRestConnectorBuilder
    {
        $this->networkInterface = $networkInterface;
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

    public function withCustomHeaders(array $customHeaders) : MobileIdRestConnectorBuilder
    {
        $this->customHeaders = $customHeaders;
        return $this;
    }

    public function withSslPinnedPublicKeys(?string $publicKeys) : MobileIdRestConnectorBuilder
    {
        $this->sslPinnedPublicKeys = $publicKeys;
        return $this;
    }

    public function build() : MobileIdRestConnector
    {
        return new MobileIdRestConnector($this);
    }
}
