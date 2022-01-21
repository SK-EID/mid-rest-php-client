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

class SessionStatusPollerBuilder
{

    private $connector;
    /** @var int $pollingSleepTimeoutSeconds */
    private int $pollingSleepTimeoutSeconds = 0;
    /** @var int $longPollingTimeoutSeconds */
    private int $longPollingTimeoutSeconds = 0;

    /**
     * @return mixed
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * @return int
     */
    public function getPollingSleepTimeoutSeconds(): int
    {
        return $this->pollingSleepTimeoutSeconds;
    }

    /**
     * @return int
     */
    public function getLongPollingTimeoutSeconds(): int
    {
        return $this->longPollingTimeoutSeconds;
    }


    public function withConnector(MobileIdConnector $connector) : SessionStatusPollerBuilder
    {
        $this->connector = $connector;
        return $this;
    }

    public function withPollingSleepTimeoutSeconds(int $pollingSleepTimeoutSeconds) : SessionStatusPollerBuilder
    {
        $this->pollingSleepTimeoutSeconds = $pollingSleepTimeoutSeconds;
        return $this;
    }

    public function withLongPollingTimeoutSeconds(int $longPollingTimeoutSeconds) : SessionStatusPollerBuilder
    {
        $this->longPollingTimeoutSeconds = $longPollingTimeoutSeconds;
        return $this;
    }

    public function build() : SessionStatusPoller
    {
        return new SessionStatusPoller($this);
    }

}
