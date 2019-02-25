<?php
require __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../mock/SessionStatusDummy.php';
require_once __DIR__ . '/../mock/MobileIdConnectorStub.php';
require_once __DIR__ . '/../mock/TestData.php';
require_once __DIR__ . '/../../ee.sk.mid/rest/SessionStatusPoller.php';

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 2:33 PM
 */
class SessionStatusPollerTest extends PHPUnit_Framework_TestCase
{
    private $connector;
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
        $this->connector->getResponse()->add(SessionStatusDummy::createCompleteSessionStatus());
        $sessionStatus = $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
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
        $this->connector->getResponses()->add(SessionStatusDummy::createRunningSessionStatus());
        $this->connector->getResponses()->add(SessionStatusDummy::createRunningSessionStatus());
        $this->connector->getResponses()->add(SessionStatusDummy::createSuccessfulSessionStatus());

        $sessionStatus = $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
        $this->assertEquals(3, $this->connector->getResponsesNumber());
        SessionStatusDummy::assertCompleteSessionStatus($sessionStatus);
    }

    /**
     * @test
     * @throws Exception
     */
    public function setPollingSleepTime()
    {
        $this->poller->setPollingSleepTimeSeconds(2);
        self::addMultipleRunningSessionResponses();
        $this->connector->getResponses()->add(SessionStatusDummy::createSuccessfulSessionStatus());
        $duration = self::measurePollingDuration();
        echo $duration;
        $this->assertEquals(true, $duration > 10000);
        $this->assertEquals(true, $duration < 10100);
    }

    /**
     * @test
     * @expectedException SessionTimeoutException
     */
    public function getUserTimeoutResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createTimeoutSessionStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException ResponseRetrievingException
     */
    public function getResponseRetrievingErrorResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createResponseRetrievingErrorStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException NotMIDClientException
     */
    public function getNotMIDClientResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createNotMIDClientStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException ExpiredException
     */
    public function getMSSSPTransactionExpiredResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createMSSPTransactionExpiredStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException UserCancellationException
     */
    public function getUserCancellationResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createUserCancellationStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException MIDNotReadyException
     */
    public function getMIDNotReadyResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createMIDNotReadyStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException SimNotAvailableException
     */
    public function getSimNotAvailableResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createSimNotAvailableStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException DeliveryException
     */
    public function getDeliveryErrorResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createDeliveryErrorStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException InvalidCardResponseException
     */
    public function getInvalidCardResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createInvalidCardResponseStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException SignatureHashMismatchException
     */
    public function getSignatureHashMismatchResponse_shouldThrowException()
    {
        $this->connector->getResponses()->add(SessionStatusDummy::createSignatureHashMismatchStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function getUnknownResult_shouldThrowException()
    {
        $sessionStatus = SessionStatusDummy::createSuccessfulSessionStatus();
        $sessionStatus->setResult("HACKERMAN");
        $this->connector->getResponses()->add($sessionStatus);
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function getMissingResult_shouldThrowException()
    {
        $sessionStatus = SessionStatusDummy::createSuccessfulSessionStatus();
        $sessionStatus->setResult(null);
        $this->connector->getResponses()->add($sessionStatus);
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
    }

    private function measurePollingDuration()
    {
        $startTime = microtime(true);
        $sessionStatus = $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID, TestData::AUTHENTICATION_SESSION_PATH);
        SessionStatusDummy::assertCompleteSessionStatus($sessionStatus);
        $endTime = microtime(true) - $startTime;
        return $endTime - $startTime;
    }

    private function addMultipleRunningSessionResponses()
    {
        for ($i = 0; $i < 5; $i++)
        {
            $this->connector->getResponses()->add(SessionStatusDummy::createRunningSessionStatus());
        }
    }

}