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
use Sk\Mid\MobileIdClient;
use Sk\Mid\Rest\MobileIdRestConnector;

class MobileIdClientBuilder
{

    /** @var string $relyingPartyUUID */
    private $relyingPartyUUID;

    /** @var string $relyingPartyName */
    private $relyingPartyName;

    /** @var string $hostUrl */
    private $hostUrl;

    /** @var string $networkConnectionConfig */
    private $networkConnectionConfig;

    /** @var int $pollingSleepTimeoutSeconds */
    private $pollingSleepTimeoutSeconds = 0;

    /** @var int $longPollingTimeoutSeconds */
    private $longPollingTimeoutSeconds = 0;

    /** @var MobileIdRestConnector $connector */
    private $connector;

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

    public function getNetworkConnectionConfig() : ?string
    {
        return $this->networkConnectionConfig;
    }

    public function getPollingSleepTimeoutSeconds() : int
    {
        return $this->pollingSleepTimeoutSeconds;
    }

    public function getLongPollingTimeoutSeconds() : int
    {
        return $this->longPollingTimeoutSeconds;
    }

    public function getConnector() : ?MobileIdRestConnector
    {
        return $this->connector;
    }

    public function withRelyingPartyUUID(?string $relyingPartyUUID) : MobileIdClientBuilder
    {
        $this->relyingPartyUUID = $relyingPartyUUID;
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

    public function withNetworkConnectionConfig(string $networkConnectionConfig) : MobileIdClientBuilder
    {
        $this->networkConnectionConfig = $networkConnectionConfig;
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

    public function build() : MobileIdClient
    {
        return new MobileIdClient($this);
    }
}
