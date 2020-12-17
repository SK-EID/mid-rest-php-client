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


    /** @test */
    public function shouldReturnConnector() {
        $client = MobileIdClient::newBuilder()
                ->withHostUrl(TestData::DEMO_HOST_URL)
                ->withSslPinnedPublicKeys("sha256//..")
                ->build();

        $this->assertThat($client->getMobileIdConnector(), $this->logicalNot($this->isNull()));
    }

}
