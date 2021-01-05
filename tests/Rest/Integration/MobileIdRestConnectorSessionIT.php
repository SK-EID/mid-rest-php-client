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
namespace Sk\Mid\Tests\Rest\Integration;
use Sk\Mid\Exception\MidSessionNotFoundException;
use Sk\Mid\Rest\Dao\Request\SessionStatusRequest;
use Sk\Mid\Rest\Dao\SessionStatus;
use Sk\Mid\Rest\MobileIdRestConnector;
use Sk\Mid\Rest\SessionStatusPoller;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Tests\Mock\MobileIdRestServiceRequestDummy;
use Sk\Mid\Tests\Mock\MobileIdRestServiceResponseDummy;
use PHPUnit\Framework\TestCase;

class MobileIdRestConnectorSessionIT extends TestCase
{
    /** @var MobileIdRestConnector $connector */
    private $connector;

    private function getConnector() : MobileIdRestConnector
    {
        return $this->connector;
    }

    protected function setUp() : void
    {
        $this->connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->build();
    }

    /**
     * @test
     */
    public function getSessionStatus_forSuccessfulAuthenticationRequest()
    {
        $authenticationRequest = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();
        MobileIdRestServiceRequestDummy::assertCorrectAuthenticationRequestMade($authenticationRequest);

        $authenticationResponse = $this->getConnector()->initAuthentication($authenticationRequest);
        assert(!is_null($authenticationResponse->getSessionId()) && !empty($authenticationResponse->getSessionId()));

        $sessionStatusRequest = new SessionStatusRequest($authenticationResponse->getSessionId());
        $poller = SessionStatusPoller::newBuilder()
                ->withConnector($this->getConnector())
                ->build();
        $sessionStatus = $poller->fetchFinalAuthenticationSession($sessionStatusRequest->getSessionId());
        MobileIdRestServiceResponseDummy::assertAuthenticationPolled($sessionStatus);
    }

    /**
     * @test
     */
    public function getSessionStatus_whenSessionStatusNotExists_shouldThrowException()
    {
        $this->expectException(MidSessionNotFoundException::class);

        $request = new SessionStatusRequest(TestData::SESSION_ID);
        $this->getConnector()->pullAuthenticationSessionStatus($request);
    }

    /**
     * @test
     */
    public function getCorrectSessionStatusResponseSocketTimeoutMs() {
        $request = new SessionStatusRequest(TestData::SESSION_ID, 2);
        self::assertEquals(2000, $request->getSessionStatusResponseSocketTimeoutMs());
    }

}
