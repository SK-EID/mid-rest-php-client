<?php
/**
 * Created by IntelliJ IDEA.
 * User: mikks
 * Date: 3/6/2019
 * Time: 2:47 PM
 */

namespace sk\mid\tests;


use PHPUnit\Framework\TestCase;
use sk\mid\exception\MidInternalErrorException;
use sk\mid\MobileIdClient;
use sk\mid\MobileIdClientBuilder;
use sk\mid\rest\dao\SessionStatus;
use sk\mid\tests\mock\TestData;

class MobileIdClientTest extends TestCase
{
    private $client;

    protected function setUp() : void
    {
        $this->client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function validateResponse_nullSessionStatus_shouldThrowException() {
        $this->client->validateResponse(null);
    }

    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function validateResponse_sessionStatusWithEmptyValue_shouldThrowException() {
        $array = array(
            'signature' => array(
                'algorithm' => 'Sha256',
                'value' => ''
            )
        );
        $sessionStatus = new SessionStatus($array);
        $this->client->validateResponse($sessionStatus);
    }
}
