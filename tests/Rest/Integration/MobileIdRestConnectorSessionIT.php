<?php
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

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 2:00 PM
 */
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
     * @throws \Exception
     */
    public function getCorrectSessionStatusResponseSocketTimeoutMs() {
        $request = new SessionStatusRequest(TestData::SESSION_ID, 2);
        self::assertEquals(2000, $request->getSessionStatusResponseSocketTimeoutMs());
    }



}
