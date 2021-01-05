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
namespace Sk\Mid\Tests\Rest;
use PHPUnit\Framework\TestCase;

use Sk\Mid\Exception\MidDeliveryException;
use Sk\Mid\Exception\MidInvalidUserConfigurationException;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Exception\MidSessionTimeoutException;
use Sk\Mid\Exception\MidNotMidClientException;
use Sk\Mid\Exception\MidPhoneNotAvailableException;
use Sk\Mid\Exception\MidUserCancellationException;
use Sk\Mid\Tests\Mock\SessionStatusDummy;
use Sk\Mid\Tests\Mock\MobileIdConnectorStub;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Rest\SessionStatusPoller;

class SessionStatusPollerTest extends TestCase
{
    /** @var MobileIdConnectorStub $connector */
    private $connector;

    /** @var SessionStatusPoller $poller */
    private $poller;

    protected function setUp() : void
    {
        $this->connector = new MobileIdConnectorStub();
        $this->poller = SessionStatusPoller::newBuilder()
                ->withConnector($this->connector)
                ->withPollingSleepTimeoutSeconds(1)
                ->build();
    }

    /**
     * @test
     */
    public function getFirstCompleteResponse()
    {
        $this->connector->addResponse(SessionStatusDummy::createSuccessfulSessionStatus());
        $sessionStatus = $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
        $this->assertEquals(TestData::SESSION_ID, $this->connector->getSessionIdUsed());
        $this->assertEquals(1, $this->connector->getResponseNumber());
        SessionStatusDummy::assertCompleteSessionStatus($sessionStatus);
    }

    /**
     * @test
     */
    public function pollAndGetThirdCompleteResponse()
    {
        $this->connector->addResponse(SessionStatusDummy::createRunningSessionStatus());
        $this->connector->addResponse(SessionStatusDummy::createRunningSessionStatus());
        $this->connector->addResponse(SessionStatusDummy::createSuccessfulSessionStatus());

        $sessionStatus = $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
        $this->assertEquals(3, $this->connector->getResponseNumber());
        SessionStatusDummy::assertCompleteSessionStatus($sessionStatus);
    }

    /**
     * @test
     */
    public function getUserTimeoutResponse_shouldThrowException()
    {
        $this->expectException(MidSessionTimeoutException::class);

        $this->connector->addResponse(SessionStatusDummy::createTimeoutSessionStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getResponseRetrievingErrorResponse_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $this->connector->addResponse(SessionStatusDummy::createResponseRetrievingErrorStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getNotMIDClientResponse_shouldThrowException()
    {
        $this->expectException(MidNotMidClientException::class);

        $this->connector->addResponse(SessionStatusDummy::createNotMIDClientStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getMSSSPTransactionExpiredResponse_shouldThrowException()
    {
        $this->expectException(MidSessionTimeoutException::class);

        $this->connector->addResponse(SessionStatusDummy::createMSSPTransactionExpiredStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getUserCancellationResponse_shouldThrowException()
    {
        $this->expectException(MidUserCancellationException::class);

        $this->connector->addResponse(SessionStatusDummy::createUserCancellationStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getMIDNotReadyResponse_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $this->expectException(MidInternalErrorException::class);

        $this->connector->addResponse(SessionStatusDummy::createMIDNotReadyStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getSimNotAvailableResponse_shouldThrowException()
    {
        $this->expectException(MidPhoneNotAvailableException::class);

        $this->connector->addResponse(SessionStatusDummy::createSimNotAvailableStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getDeliveryErrorResponse_shouldThrowException()
    {
        $this->expectException(MidDeliveryException::class);

        $this->connector->addResponse(SessionStatusDummy::createDeliveryErrorStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getInvalidCardResponse_shouldThrowException()
    {
        $this->expectException(MidDeliveryException::class);

        $this->connector->addResponse(SessionStatusDummy::createInvalidCardResponseStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getSignatureHashMismatchResponse_shouldThrowException()
    {
        $this->expectException(MidInvalidUserConfigurationException::class);

        $this->connector->addResponse(SessionStatusDummy::createSignatureHashMismatchStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getUnknownResult_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $sessionStatus = SessionStatusDummy::createSuccessfulSessionStatus();
        $sessionStatus->setResult("HACKERMAN");
        $this->connector->addResponse($sessionStatus);
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getMissingResult_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $sessionStatus = SessionStatusDummy::createSuccessfulSessionStatus();
        $sessionStatus->setResult(null);
        $this->connector->addResponse($sessionStatus);
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

}
