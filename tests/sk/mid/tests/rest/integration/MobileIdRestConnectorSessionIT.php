<?php
namespace sk\mid\tests\rest\integration;
use sk\mid\exception\MidSessionNotFoundException;
use sk\mid\rest\dao\request\SessionStatusRequest;
use sk\mid\rest\dao\SessionStatus;
use sk\mid\rest\MobileIdRestConnector;
use sk\mid\rest\SessionStatusPoller;
use sk\mid\tests\mock\TestData;
use sk\mid\tests\mock\MobileIdRestServiceRequestDummy;
use sk\mid\tests\mock\MobileIdRestServiceResponseDummy;
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

        $authenticationResponse = $this->getConnector()->authenticate($authenticationRequest);
        assert(!is_null($authenticationResponse->getSessionId()) && !empty($authenticationResponse->getSessionId()));

        $sessionStatusRequest = new SessionStatusRequest($authenticationResponse->getSessionId());
        $poller = new SessionStatusPoller($this->getConnector());
        $sessionStatus = $poller->fetchFinalAuthenticationSession($sessionStatusRequest->getSessionId());
        MobileIdRestServiceResponseDummy::assertAuthenticationPolled($sessionStatus);
    }

    /**
     * @test
     * @expectedException MidSessionNotFoundException
     */
    public function getSessionStatus_whenSessionStatusNotExists_shouldThrowException()
    {
        $request = new SessionStatusRequest(TestData::SESSION_ID);
        $this->getConnector()->getAuthenticationSessionStatus($request);
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
