<?php
namespace Sk\Mid\Tests\Rest;
use PHPUnit\Framework\TestCase;

use Sk\Mid\Exception\DeliveryException;
use Sk\Mid\Exception\InvalidUserConfigurationException;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Exception\MidSessionTimeoutException;
use Sk\Mid\Exception\NotMidClientException;
use Sk\Mid\Exception\PhoneNotAvailableException;
use Sk\Mid\Exception\UserCancellationException;
use Sk\Mid\Tests\Mock\SessionStatusDummy;
use Sk\Mid\Tests\Mock\MobileIdConnectorStub;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Rest\SessionStatusPoller;


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
        $this->expectException(NotMidClientException::class);

        $this->expectException(NotMidClientException::class);

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
        $this->expectException(UserCancellationException::class);

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
        $this->expectException(PhoneNotAvailableException::class);

        $this->connector->addResponse(SessionStatusDummy::createSimNotAvailableStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getDeliveryErrorResponse_shouldThrowException()
    {
        $this->expectException(DeliveryException::class);

        $this->connector->addResponse(SessionStatusDummy::createDeliveryErrorStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getInvalidCardResponse_shouldThrowException()
    {
        $this->expectException(DeliveryException::class);

        $this->connector->addResponse(SessionStatusDummy::createInvalidCardResponseStatus());
        $this->poller->fetchFinalSessionStatus(TestData::SESSION_ID);
    }

    /**
     * @test
     */
    public function getSignatureHashMismatchResponse_shouldThrowException()
    {
        $this->expectException(InvalidUserConfigurationException::class);

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
