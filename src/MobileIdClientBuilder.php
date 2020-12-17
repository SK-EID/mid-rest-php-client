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
use Sk\Mid\Rest\MobileIdRestConnector;

class MobileIdClientBuilder
{

    /** @var string $relyingPartyUUID */
    private $relyingPartyUUID;

    /** @var string $relyingPartyName */
    private $relyingPartyName;

    /** @var string $hostUrl */
    private $hostUrl;

    /** @var string $networkInterface */
    private $networkInterface = "";

    /** @var int $pollingSleepTimeoutSeconds */
    private $pollingSleepTimeoutSeconds = 0;

    /** @var int $longPollingTimeoutSeconds */
    private $longPollingTimeoutSeconds = 0;

    /** @var MobileIdRestConnector $connector */
    private $connector;

    /** @var string $sslPinnedPublicKeys */
    private $sslPinnedPublicKeys = "";

    /** @var array $customHeaders */
    private $customHeaders = array();

    public function __construct()
    {
    }

    public function getRelyingPartyUUID() : ?string
    {
        return $this->relyingPartyUUID;
    }

    public function getRelyingPartyName() : ?string
    {
        return $this->relyingPartyName;
    }

    public function getHostUrl() : ?string
    {
        return $this->hostUrl;
    }

    public function getNetworkInterface() : ?string
    {
        return $this->networkInterface;
    }

    public function getPollingSleepTimeoutSeconds() : int
    {
        return $this->pollingSleepTimeoutSeconds;
    }

    public function getLongPollingTimeoutSeconds() : int
    {
        return $this->longPollingTimeoutSeconds;
    }

    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }

    public function getConnector() : ?MobileIdRestConnector
    {
        return $this->connector;
    }

    /**
     * @return string
     */
    public function getSslPinnedPublicKeys(): string
    {
        return $this->sslPinnedPublicKeys;
    }

    public function withRelyingPartyUUID(?string $relyingPartyUUID) : MobileIdClientBuilder
    {
        $this->relyingPartyUUID = $relyingPartyUUID;
        return $this;
    }

    public function withCustomHeaders(?array $customHeaders) :MobileIdClientBuilder
    {
        $this->customHeaders = $customHeaders;
        return $this;
    }

    public function withRelyingPartyName(?string $relyingPartyName) : MobileIdClientBuilder
    {
        $this->relyingPartyName = $relyingPartyName;
        return $this;
    }

    public function withHostUrl(string $hostUrl) : MobileIdClientBuilder
    {
        $this->hostUrl = $hostUrl;
        return $this;
    }

    /**
     * @see https://curl.se/libcurl/c/CURLOPT_INTERFACE.html
     *
     * @param string $networkInterface
     * @return $this
     */
    public function withNetworkInterface(string $networkInterface) : MobileIdClientBuilder
    {
        $this->networkInterface = $networkInterface;
        return $this;
    }

    public function withPollingSleepTimeoutSeconds(int $pollingSleepTimeoutSeconds) : MobileIdClientBuilder
    {
        $this->pollingSleepTimeoutSeconds = $pollingSleepTimeoutSeconds;
        return $this;
    }

    public function withLongPollingTimeoutSeconds(int $longPollingTimeoutSeconds) : MobileIdClientBuilder
    {
        $this->longPollingTimeoutSeconds = $longPollingTimeoutSeconds;
        return $this;
    }

    public function withMobileIdConnector(MobileIdRestConnector $mobileIdConnector) : MobileIdClientBuilder
    {
        $this->connector = $mobileIdConnector;
        return $this;
    }

    /**
     * @see https://curl.se/libcurl/c/CURLOPT_PINNEDPUBLICKEY.html
     *
     * @param string $sslPinnedPublicKeys
     * @return $this
     */
    public function withSslPinnedPublicKeys(string $sslPinnedPublicKeys) : MobileIdClientBuilder
    {
        $this->sslPinnedPublicKeys = $sslPinnedPublicKeys;
        return $this;
    }

    public function build() : MobileIdClient
    {
        return new MobileIdClient($this);
    }
}
