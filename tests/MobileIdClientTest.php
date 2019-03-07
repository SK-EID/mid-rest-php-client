<?php
/**
 * Created by IntelliJ IDEA.
 * User: mikks
 * Date: 3/6/2019
 * Time: 2:47 PM
 */

namespace Sk\Mid\Tests;

use PHPUnit\Framework\TestCase;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Tests\Mock\TestData;

class MobileIdClientTest extends TestCase
{

    protected function setUp() : void
    {
        $this->client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->build();
    }

    /** @test */
    public function shouldReturnConnector() {
        $client = MobileIdClient::newBuilder()
                ->withHostUrl(TestData::DEMO_HOST_URL)
                ->build();

        $this->assertThat($client->getMobileIdConnector(), $this->logicalNot($this->isNull()));
    }

}
