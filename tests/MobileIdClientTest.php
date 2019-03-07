<?php
/**
 * Created by IntelliJ IDEA.
 * User: mikks
 * Date: 3/6/2019
 * Time: 2:47 PM
 */

namespace Sk\Mid\Tests;


use PHPUnit\Framework\TestCase;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\MobileIdClient;
use Sk\Mid\MobileIdClientBuilder;
use Sk\Mid\Rest\Dao\SessionStatus;
use Sk\Mid\Tests\Mock\TestData;

class MobileIdClientTest extends TestCase
{
    private $client;

    private function getClient() : MobileIdClient
    {
        return $this->client;
    }

    protected function setUp() : void
    {
        $this->client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();
    }


}
