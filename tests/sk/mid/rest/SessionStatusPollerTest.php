<?php

use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../../../vendor/autoload.php';

require_once __DIR__ . '/../mock/SessionStatusDummy.php';
require_once __DIR__ . '/../mock/MobileIdConnectorStub.php';
require_once __DIR__ . '/../mock/TestData.php';
require_once __DIR__ . '/../../../../src/sk/mid/rest/SessionStatusPoller.php';

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 2:33 PM
 */
class SessionStatusPollerTest extends TestCase
{
    /** @var MobileIdConnectorStub $connector */
    private $connector;

    /** @var SessionStatusPoller $poller */
    private $poller;

    protected function setUp()
    {
        $this->connector = new MobileIdConnectorStub();
        $this->poller = new SessionStatusPoller($this->connector);
        $this->poller->setPollingSleepTimeSeconds(1);
    }

    /**
     * @test
     * @throws Exception
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
     * @throws Exception
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
     * @expectedException MidSessionTimeoutException
     */
    public function getUserTimeoutResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createTimeoutSessionStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function getResponseRetrievingErrorResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createResponseRetrievingErrorStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException NotMidClientException
     */
    public function getNotMIDClientResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createNotMIDClientStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException MidSessionTimeoutException
     */
    public function getMSSSPTransactionExpiredResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createMSSPTransactionExpiredStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException UserCancellationException
     */
    public function getUserCancellationResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createUserCancellationStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function getMIDNotReadyResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createMIDNotReadyStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException PhoneNotAvailableException
     */
    public function getSimNotAvailableResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createSimNotAvailableStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException DeliveryException
     */
    public function getDeliveryErrorResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createDeliveryErrorStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException DeliveryException
     */
    public function getInvalidCardResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createInvalidCardResponseStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException InvalidUserConfigurationException
     */
    public function getSignatureHashMismatchResponse_shouldThrowException()
    {
        $this->connector->addResponse(SessionStatusDummy::createSignatureHashMismatchStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function getUnknownResult_shouldThrowException()
    {
        $sessionStatus = SessionStatusDummy::createSuccessfulSessionStatus();
        $sessionStatus->setResult("HACKERMAN");
        $this->connector->addResponse($sessionStatus);
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function getMissingResult_shouldThrowException()
    {
        $sessionStatus = SessionStatusDummy::createSuccessfulSessionStatus();
        $sessionStatus->setResult(null);
        $this->connector->addResponse($sessionStatus);
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    private function measurePollingDuration()
    {
        $startTime = microtime(true);
        $sessionStatus = $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
        SessionStatusDummy::assertCompleteSessionStatus($sessionStatus);
        $endTime = microtime(true) - $startTime;
        return $endTime - $startTime;
    }

    private function addMultipleRunningSessionResponses()
    {
        $responses = $this->connector->getResponses();
        for ($i = 0; $i < 5; $i++)
        {
            array_push($responses, SessionStatusDummy::createRunningSessionStatus());
        }
    }

}
