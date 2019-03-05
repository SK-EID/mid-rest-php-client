<?php
require_once __DIR__ . '/../../../ee.sk.mid/rest/MobileIdRestConnector.php';
require_once __DIR__ . '/../../../ee.sk.mid/rest/SessionStatusPoller.php';
require_once __DIR__ . '/../../mock/TestData.php';
require_once __DIR__ . '/../../mock/MobileIdRestServiceRequestDummy.php';
require_once __DIR__ . '/../../mock/MobileIdRestServiceResponseDummy.php';
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 2:00 PM
 */
class MobileIdRestConnectorSessionIT extends TestCase
{
    private $connector;

    private function getConnector() : MobileIdRestConnector
    {
        return $this->connector;
    }

    protected function setUp()
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

}
